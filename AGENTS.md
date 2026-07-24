## Agent guide (humans + AI)

This repository is a Laravel package with strict conventions and a stable Developer API.

### Before changing anything

- Read the project rules in:
  - `.ai/instructions.md`
  - `.ai/philosophy.md`
  - `.ai/boundaries.md`
  - `.ai/architecture.md`
  - `.ai/coding-style.md`
  - `.ai/testing.md`
  - `.ai/package-development.md`
  - `.ai/pr-review.md` (when reviewing or opening PRs)

### Core rules

- All usage/documentation must go through the public API (`Trustbird::domain()->action(...)`), not direct Eloquent writes.
- Public APIs must support PHP named arguments (parameter names are part of the API).
- Prefer additive migrations.
- Keep models as persistence objects (casts + relationships + simple helpers only).
- Maintain 100% test coverage.
- Put shared AI guidance only in `.ai/` — never editor-specific agent config (no `.cursor/` skills/rules, etc.).
- After creating any new pull request, run the review in `.ai/pr-review.md` before considering the PR ready.

### Before finishing any task

Always run through `.ai/release-checklist.md` before you consider the task done.

For preparing a release PR, follow `.ai/release-flow.md` and run:

```bash
composer release:prepare -- <version>
```

Minimum for most changes:

1. Run tests (and coverage when PHP code changed).
2. Update domain docs (`docs/02-usage/*.md`) and `CHANGELOG.md` for user-visible changes.

Docs navigation sync (`docs/index.md`, introduction headings from `docs/navigation.php`) runs automatically as part of `composer release:prepare`.

### Common commands

- Run tests: `composer test`
- Run coverage: `XDEBUG_MODE=coverage ./vendor/bin/pest --coverage --min=100`
- Prepare release PR: `composer release:prepare -- <version>`
