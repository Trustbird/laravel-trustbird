# Package Development

This repository is a Composer package.

## Namespace

The root namespace is:

`Trustbird\`

## Configuration

Configuration lives in:

`config/trustbird.php`

## Commands

All Artisan commands must start with:

`trustbird:`

## Migrations

Migrations must be safe for package users.

Prefer additive migrations.

Avoid destructive migrations unless explicitly documented.

## Service providers

Service providers should only register and bootstrap package functionality.

Do not place business logic in service providers.

## Public API

Everything exposed to users should be considered stable.

This includes:

- configuration keys
- commands
- contracts
- events
- published resources
- documented classes
- documented methods

Internal classes may evolve more freely.

## Extension points

Extension points should be intentional.

When exposing extension points, document:

- purpose
- expected input
- expected output
- stability guarantees
- example usage

## Documentation

Whenever behaviour changes:

- update documentation
- update examples
- update tests
- update changelog

Documentation is part of the product.

When adding or changing documentation folders:

- add markdown files under `docs/NN-name/`
- register the folder in `docs/navigation.php` with the intended introduction heading
- docs navigation sync (`docs/index.md`, introduction sections) is handled by `composer release:prepare`

## Release mindset

Before releasing, complete `.ai/release-checklist.md`.

At minimum, verify:

- tests pass
- coverage is 100%
- static analysis passes
- documentation is up to date (including docs index sync)
- changelog is updated
- migrations are safe
- public API changes are intentional

## Releases and tags

`main` is protected and must only be updated through pull requests.

This repository uses `CHANGELOG.md` as the source of truth for releases.

Rules:

- Release headers MUST have an ISO date (YYYY-MM-DD).
- Release headers MUST NOT use `Unpublished`.
- The tag format is `v<version>` (example: `v0.1.0-alpha.4`).

Process:

- Prepare a release PR that updates `CHANGELOG.md` (top entry = the version being released + ISO date).
- Merge the release PR into `main`.
- A GitHub Actions workflow will create and push the tag automatically after the merge.

AI/human release prepare command:

```bash
composer release:prepare -- <version>
```

See `.ai/release-flow.md` for the full agent-driven release flow.
