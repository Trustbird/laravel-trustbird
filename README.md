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

## Release and tagging

`main` is protected and must only be updated through pull requests.

This repository uses `CHANGELOG.md` as the source of truth for releases.

- The latest release entry MUST use an ISO date (YYYY-MM-DD).
- Release entries MUST NOT use `Unpublished`.
- After a release PR is merged to `main`, a GitHub Actions workflow automatically creates a tag.
  - Tag format: `v<version>` (example: `v0.1.0-alpha.4`)
