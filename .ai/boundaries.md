# Package Boundaries

This repository contains the Laravel core of Trustbird.

The purpose of this package is to provide the complete backend foundation that other Laravel applications can build upon.

## Core responsibilities

The core package may contain:

- domain models
- actions
- services
- contracts
- events
- listeners
- policies
- validation rules
- configuration
- migrations
- console commands
- queues
- scheduled tasks
- API resources
- data transfer objects
- value objects
- extension points
- package service providers

## Non-goals

The core package must not implement frontend technologies.

Do not add:

- user interfaces
- dashboards
- admin panels
- frontend assets
- views
- CSS
- JavaScript
- client-side frameworks
- UI-specific components
- application-specific presentation logic

## Design principle

The core should be usable in any Laravel application.

Developers should be free to build their own user interface using any frontend technology they choose.

The core must never assume how the application is presented to the user.

## Dependency direction

Dependencies must always point inward.

The domain must never depend on presentation.

Presentation layers may depend on the core.

The core must never depend on application-specific code.

## Boundary test

Before adding code, ask:

Can this be used without a frontend?

If the answer is no, it does not belong in this repository.
