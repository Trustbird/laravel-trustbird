# Project Instructions

These instructions apply to every change made to this repository.

## Package identity

This repository contains the Laravel core package for Trustbird.

It provides the backend foundation only.

It must not implement frontend technologies.

## Technology

- Laravel 12+
- PHP 8.2+
- PestPHP
- Composer package
- SQLite compatible
- MySQL compatible

## General principles

- Follow Laravel conventions.
- Keep the codebase simple.
- Prefer readability over cleverness.
- Never introduce unnecessary abstractions.
- Never introduce unnecessary Composer dependencies.
- Prefer framework features over custom implementations.
- Write maintainable code for long-term support.
- Keep the package usable inside existing Laravel applications.

## Code quality

Always:

- use strict typing
- use explicit return types
- use constructor property promotion where useful
- keep methods small
- keep classes focused
- avoid duplicated code
- write expressive names

Never:

- leave dead code
- leave TODO comments
- comment obvious code
- use magic values
- introduce frontend logic
- introduce UI-specific assumptions

## Backwards compatibility

Public APIs must remain backwards compatible unless a breaking change is intentional and documented.

Breaking changes require:

- upgrade documentation
- changelog entry
- migration strategy
- tests covering the new behaviour

## Language

Everything must be written in English.

This includes:

- code
- comments
- documentation (must always be up-to-date with the current state of the project)
- commit messages
- exception messages
- test names
