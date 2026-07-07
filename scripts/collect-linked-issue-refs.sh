#!/usr/bin/env bash

# Collect issue numbers linked to branches whose tip commits are in a git range.
# Outputs one issue number per line.

set -euo pipefail

RANGE="${1:-HEAD}"

if ! command -v gh >/dev/null 2>&1; then
  exit 0
fi

if ! command -v jq >/dev/null 2>&1; then
  exit 0
fi

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

if ! git rev-parse --verify "${RANGE%%..*}" >/dev/null 2>&1; then
  exit 0
fi

COMMIT_SHAS="$(git rev-list "$RANGE")"

QUERY='
query($owner: String!, $name: String!, $after: String) {
  repository(owner: $owner, name: $name) {
    issues(states: [OPEN, CLOSED], first: 100, after: $after) {
      pageInfo { hasNextPage endCursor }
      nodes {
        number
        linkedBranches(first: 20) {
          nodes {
            ref {
              target {
                ... on Commit { oid }
              }
            }
          }
        }
      }
    }
  }
}
'

OWNER="$(gh repo view --json owner -q .owner.login)"
NAME="$(gh repo view --json name -q .name)"
CURSOR=""
TMP="$(mktemp)"

while true; do
  if [[ -z "$CURSOR" ]]; then
    RESPONSE="$(gh api graphql -f query="$QUERY" -f owner="$OWNER" -f name="$NAME")"
  else
    RESPONSE="$(gh api graphql -f query="$QUERY" -f owner="$OWNER" -f name="$NAME" -f after="$CURSOR")"
  fi

  echo "$RESPONSE" | jq -r '
    .data.repository.issues.nodes[] |
    .number as $num |
    .linkedBranches.nodes[].ref.target.oid as $oid |
    select($oid != null) |
    "\($num) \($oid)"
  ' >> "$TMP"

  HAS_NEXT="$(echo "$RESPONSE" | jq -r '.data.repository.issues.pageInfo.hasNextPage')"
  if [[ "$HAS_NEXT" != "true" ]]; then
    break
  fi

  CURSOR="$(echo "$RESPONSE" | jq -r '.data.repository.issues.pageInfo.endCursor')"
done

while IFS=' ' read -r issue_number oid; do
  [[ -z "${issue_number:-}" || -z "${oid:-}" ]] && continue
  if grep -qx "$oid" <<< "$COMMIT_SHAS"; then
    echo "$issue_number"
  fi
done < "$TMP" | sort -nu

rm -f "$TMP"
