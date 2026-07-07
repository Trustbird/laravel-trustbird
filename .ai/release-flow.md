# AI release flow

Use this flow when a set of features is ready to ship and you need a release PR to `main`.

This is an **agent-driven** flow. It does not require a dedicated GitHub Actions docs workflow.

## When to run

- All feature branches are merged (or the release branch already contains the intended commits).
- `CHANGELOG.md` content for the release is drafted (or will be added by the prepare script).
- Tests and coverage are green on the base branch.

## One-command prepare

From a clean working tree on the base branch:

```bash
composer release:prepare -- 0.1.0-alpha.5
```

Dry run first if you want to inspect actions:

```bash
composer release:prepare -- --dry-run 0.1.0-alpha.5
```

## What the script does

`scripts/prepare-release.sh` will:

1. Validate the version as SemVer (`0.1.0-alpha.5`).
2. Update `main` from `origin`.
3. Create `release/v<version>` from `main`.
4. Ensure `CHANGELOG.md` has `## [<version>] - Unpublished` (adds a stub when missing).
5. Sync docs navigation from `docs/navigation.php` (`docs/index.md`, introduction headings) and verify consistency.
6. Run `composer test`.
7. Commit release prep changes (changelog + docs index).
8. Push the release branch.
9. Open a PR to `main` titled `Release v<version>`.
10. Pre-fill the PR body with a `Closes #...` line from:
    - `#123` references in commits since the latest tag
    - GitHub issues that have a **linked development branch** whose tip commit is included in the release

## After the PR is opened

GitHub automations take over:

- **PR autoclose** (`.github/workflows/pr-autoclose.yml`): refreshes the `Closes #...` footer from commit messages and issues with linked development branches.
- **Release changelog date** (`.github/workflows/release-changelog.yml`): on push to `release/v*`, replaces `Unpublished` with an ISO date.
- **Release gating** (`.github/workflows/tests.yml`): PR checks fail if the changelog header for the release version is not publishable.
- **Tagging** (`.github/workflows/release-tag.yml`): after merge to `main`, creates `v<version>` from the top dated changelog entry.

## Agent checklist

Before running the script:

- [ ] Complete `.ai/release-checklist.md` for the code/doc changes in this release.
- [ ] Confirm the version bump matches `README.md` SemVer rules.
- [ ] Ensure user-visible changes are listed under the new changelog section.

After running the script:

- [ ] Verify the PR was created and CI is running.
- [ ] Confirm docs index links are complete.
- [ ] Review the generated `Closes #...` list and edit the PR description if needed.

## Manual fallback

If `gh` is not available, run:

```bash
scripts/prepare-release.sh 0.1.0-alpha.5 --no-pr
```

Then create the PR manually in GitHub from `release/v0.1.0-alpha.5` to `main`.

## Options

```bash
scripts/prepare-release.sh <version> [--base main] [--dry-run] [--skip-tests] [--no-push] [--no-pr]
```

- `--base`: branch to release from (default `main`)
- `--dry-run`: print commands without executing git/gh changes
- `--skip-tests`: skip `composer test`
- `--no-push`: only create the local branch/commits
- `--no-pr`: push branch but do not open a PR
