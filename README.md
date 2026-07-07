[![Tests](https://github.com/Trustbird/laravel-trustbird/actions/workflows/tests.yml/badge.svg)](https://github.com/Trustbird/laravel-trustbird/actions/workflows/tests.yml)
![Coverage](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/vanderbake/237304c70c4db5294e4fbe0a5afc2874/raw/laravel-trustbird-coverage.json)

# Laravel Trustbird

Laravel Trustbird is the core Laravel package for Trustbird.

It provides the backend foundation for building Trustbird-powered applications.

Current modules include:
- **People**: Manage personnel, employment types, and statuses.
- **Assets**: Combined management of Assets and Devices (Laptops, Servers, etc.).

This package is intentionally frontend-agnostic.

Developers may use this package inside existing Laravel applications, custom business systems, API-first applications, or any other Laravel-based implementation.

## Development guidelines

Before making changes, read the AI and contributor guidelines in:

- `.ai/instructions.md`
- `.ai/philosophy.md`
- `.ai/boundaries.md`
- `.ai/architecture.md`
- `.ai/coding-style.md`
- `.ai/testing.md`
- `.ai/package-development.md`

These documents define the package scope, architecture, coding rules, testing policy, and development workflow.

They are intended for both human contributors and AI coding assistants.

See also `AGENTS.md` for a short, actionable checklist (for humans and AI agents).

## Semantic Versioning

This project follows Semantic Versioning (\(MAJOR.MINOR.PATCH\)) with pre-release identifiers (example: `0.1.0-alpha.4`).

- **MAJOR**: Breaking changes to the public developer API (method signatures/parameter names), configuration keys, migrations that require manual intervention, or removal/renames of public contracts and documented behaviour.
- **MINOR**: Backwards-compatible additions (new domains/managers/models), new optional configuration, additive migrations, new documented functionality.
- **PATCH**: Backwards-compatible bug fixes, documentation fixes, internal refactors that do not change the public API.

## Release and tagging

`main` is protected and must only be updated through pull requests.

This repository uses `CHANGELOG.md` as the source of truth for releases.

- After a release PR is merged to `main`, a GitHub Actions workflow automatically creates a tag.
  - Tag format: `v<version>` (example: `v0.1.0-alpha.4`)

Automations:

- **Release prepare (local/AI)**: `composer release:prepare -- <version>` creates `release/v*`, syncs docs, runs tests, and opens a PR (see `.ai/release-flow.md`).
- **PR issue autoclose**: PR bodies are updated with a `Closes #...` footer based on `#123` references in PR titles/bodies and commit messages.
- **Release changelog date**: on pushes to `release/v*` branches, the `CHANGELOG.md` header for that version is converted from `Unpublished` to an ISO date.
- **Release gating**: PRs from `release/v*` branches into `main` fail CI if the changelog header for the release does not contain an ISO date.
- **Tagging**: after merge to `main`, a tag `v<version>` is created from the latest publishable changelog header.
