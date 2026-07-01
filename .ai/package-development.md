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

## Release mindset

Before releasing, verify:

- tests pass
- coverage is 100%
- static analysis passes
- documentation is up to date
- changelog is updated
- migrations are safe
- public API changes are intentional
