# Release and finish checklist

Run through this checklist before marking any task as done.

## Code quality

- [ ] `composer test` passes
- [ ] If PHP code changed: `XDEBUG_MODE=coverage ./vendor/bin/pest --coverage --min=100`
- [ ] Public API changes are intentional and use named arguments
- [ ] Do not add editor-specific AI config; keep shared rules in `.ai/` only
- [ ] New domains/managers are wired in A–Z order in:
  - `src/TrustbirdManager.php`
  - `src/Facades/Trustbird.php`
  - `src/TrustbirdServiceProvider.php`
  - `config/trustbird.php`
  - `tests/Feature/DeveloperApi/GeneralApiTest.php`
  - `tests/Feature/CoverageTest.php` (enums)

## Documentation

For any behaviour change:

- [ ] Domain usage doc updated (`docs/02-usage/*.md`)
- [ ] Examples use the public Developer API (`Trustbird::...()`)
- [ ] `CHANGELOG.md` updated when the change is user-visible

Docs navigation sync happens during release prepare (`composer release:prepare`) based on `docs/navigation.php`.

## Pull requests

When a PR is created (feature, fix, or release):

- [ ] Run `.ai/pr-review.md` on the new PR immediately
- [ ] Address Critical and Important findings (or explicitly defer with rationale)

## Release-specific (release branches only)

- [ ] Branch name is `release/v<semver>` (example: `release/v0.1.0-alpha.4`)
- [ ] `CHANGELOG.md` has `## [<version>] - YYYY-MM-DD` (not `Unpublished`)
- [ ] Version bump matches SemVer rules in `README.md`
