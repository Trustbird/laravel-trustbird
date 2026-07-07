#!/usr/bin/env bash

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

VERSION=""
BASE="main"
DRY_RUN=0
SKIP_TESTS=0
NO_PUSH=0
NO_PR=0

usage() {
  cat <<'EOF'
Prepare a Trustbird release branch and open a PR to main.

Usage:
  scripts/prepare-release.sh <version> [options]

Options:
  --base <branch>   Base branch to release from (default: main)
  --dry-run         Print actions without changing git state
  --skip-tests      Skip composer test
  --no-push         Create branch/commits locally only
  --no-pr           Push branch but do not create a PR

Examples:
  scripts/prepare-release.sh 0.1.0-alpha.5
  composer release:prepare -- 0.1.0-alpha.5
EOF
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    --base)
      BASE="${2:-}"
      shift 2
      ;;
    --dry-run)
      DRY_RUN=1
      shift
      ;;
    --skip-tests)
      SKIP_TESTS=1
      shift
      ;;
    --no-push)
      NO_PUSH=1
      shift
      ;;
    --no-pr)
      NO_PR=1
      shift
      ;;
    -h|--help)
      usage
      exit 0
      ;;
    -*)
      echo "Unknown option: $1" >&2
      usage >&2
      exit 1
      ;;
    *)
      if [[ -n "$VERSION" ]]; then
        echo "Unexpected argument: $1" >&2
        usage >&2
        exit 1
      fi
      VERSION="$1"
      shift
      ;;
  esac
done

if [[ -z "$VERSION" ]]; then
  usage >&2
  exit 1
fi

SEMVER_RE='^(0|[1-9][0-9]*)[.](0|[1-9][0-9]*)[.](0|[1-9][0-9]*)(-[0-9A-Za-z-]+([.][0-9A-Za-z-]+)*)?([+][0-9A-Za-z-]+([.][0-9A-Za-z-]+)*)?$'
if ! [[ "$VERSION" =~ $SEMVER_RE ]]; then
  echo "Invalid SemVer version: $VERSION" >&2
  echo "Expected something like: 0.1.0-alpha.5" >&2
  exit 1
fi

BRANCH="release/v${VERSION}"

run() {
  if [[ "$DRY_RUN" -eq 1 ]]; then
    printf '+'
    printf ' %q' "$@"
    printf '\n'
    return 0
  fi

  "$@"
}

ensure_changelog_header() {
  local changelog="$ROOT/CHANGELOG.md"
  local header="## [${VERSION}] - Unpublished"

  if grep -qE "^## \\[${VERSION}\\] - " "$changelog"; then
    echo "CHANGELOG.md already contains a header for [${VERSION}]."
    return 0
  fi

  echo "Adding changelog header: ${header}"

  if [[ "$DRY_RUN" -eq 1 ]]; then
    return 0
  fi

  php -r '
    $path = $argv[1];
    $header = $argv[2];
    $contents = file_get_contents($path);
    $needle = "The format is based on Keep a Changelog and this project adheres to Semantic Versioning.\n\n";
    $insert = $needle.$header."\n\n### Added\n\n-\n\n";
    if (! str_contains($contents, $needle)) {
        fwrite(STDERR, "Could not locate changelog preamble in CHANGELOG.md\n");
        exit(1);
    }
    file_put_contents($path, str_replace($needle, $insert, $contents, 1));
  ' "$changelog" "$header"
}

collect_issue_refs() {
  local range="${1:-}"
  local log_cmd=(git log --pretty=format:%s%n%b)

  if [[ -n "$range" ]]; then
    log_cmd+=("$range")
  else
    log_cmd+=(HEAD)
  fi

  "${log_cmd[@]}" \
    | grep -Eo '#[0-9]+' \
    | sed 's/#//' \
    | sort -nu
}

collect_all_issue_refs() {
  local range="${1:-}"
  {
    collect_issue_refs "$range"
    if [[ -x "$ROOT/scripts/collect-linked-issue-refs.sh" ]]; then
      bash "$ROOT/scripts/collect-linked-issue-refs.sh" "$range"
    fi
  } | sort -nu | paste -sd, - || true
}

LAST_TAG="$(git describe --tags --abbrev=0 2>/dev/null || true)"
RANGE=""
if [[ -n "$LAST_TAG" ]]; then
  RANGE="${LAST_TAG}..${BASE}"
fi

ISSUE_NUMBERS="$(collect_all_issue_refs "$RANGE")"
CLOSES_LINE="Closes (none detected)"
if [[ -n "$ISSUE_NUMBERS" ]]; then
  CLOSES_LINE="Closes $(echo "$ISSUE_NUMBERS" | sed 's/,/, #/g' | sed 's/^/#/')"
fi

PR_BODY="$(cat <<EOF
## Summary

Prepare release \`${VERSION}\`.

## Test plan

- [ ] CI passes
- [ ] Changelog reviewed
- [ ] Docs index is in sync

${CLOSES_LINE}
EOF
)"

echo "Preparing release ${VERSION} from ${BASE} -> ${BRANCH}"

if [[ "$DRY_RUN" -eq 0 ]] && ! git diff --quiet; then
  echo "Working tree has uncommitted changes. Commit or stash them first." >&2
  exit 1
fi

run git fetch origin "$BASE"
run git checkout "$BASE"
run git pull --ff-only origin "$BASE"

if git show-ref --verify --quiet "refs/heads/${BRANCH}"; then
  echo "Branch already exists locally: ${BRANCH}" >&2
  exit 1
fi

if git ls-remote --exit-code --heads origin "$BRANCH" >/dev/null 2>&1; then
  echo "Branch already exists on origin: ${BRANCH}" >&2
  exit 1
fi

run git checkout -b "$BRANCH"

ensure_changelog_header
run php scripts/sync-docs-index.php
run php scripts/sync-docs-index.php --check

if [[ "$SKIP_TESTS" -eq 0 ]]; then
  run composer test
fi

if [[ "$DRY_RUN" -eq 0 ]] && ! git diff --quiet; then
  git add CHANGELOG.md docs/index.md docs/01-getting-started/00-introduction.md
  git commit -m "chore: prepare release ${VERSION}"
fi

if [[ "$NO_PUSH" -eq 0 ]]; then
  run git push -u origin "$BRANCH"
fi

if [[ "$NO_PR" -eq 0 && "$NO_PUSH" -eq 0 ]]; then
  if [[ "$DRY_RUN" -eq 1 ]]; then
    run gh pr create \
      --base "$BASE" \
      --head "$BRANCH" \
      --title "Release v${VERSION}" \
      --body "$PR_BODY"
  elif ! command -v gh >/dev/null 2>&1; then
    echo "gh CLI not found. Branch pushed; create the PR manually." >&2
    exit 0
  else
    run gh pr create \
      --base "$BASE" \
      --head "$BRANCH" \
      --title "Release v${VERSION}" \
      --body "$PR_BODY"
  fi
fi

if [[ "$DRY_RUN" -eq 1 ]]; then
  cat <<EOF

Dry run complete for ${BRANCH}.
Re-run without --dry-run to execute these steps.
EOF
  exit 0
fi

cat <<EOF

Release branch ready: ${BRANCH}
Next steps:
- Wait for CI on the PR
- On push, the release changelog workflow may replace "Unpublished" with today's date
- The PR autoclose workflow will refresh the Closes footer from commits
- After merge to main, the release tag workflow creates v${VERSION}
EOF
