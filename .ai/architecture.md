# Architecture

This package is modular, backend-only, and Laravel-native.

Business logic should remain isolated, testable, and independent from presentation concerns.

## Layers

The preferred dependency flow is:

Application entry points

↓

Actions

↓

Domain

↓

Persistence

## Business logic

Business logic never belongs in:

- controllers
- commands
- service providers
- models
- API resources
- validation rules

Business logic belongs in:

- actions
- services
- domain objects
- value objects

## Models

Models represent persistence.

Models may contain relationships, casts, scopes, and simple accessors.

Models must not become business logic containers.

## Actions

Actions perform business operations.

One action performs one task.

Actions should be easy to test directly.

Every Action must dispatch a corresponding Event upon completion.

## Services

Services coordinate behaviour that spans multiple actions or external concerns.

Services should not become god objects.

## Events

Events should describe something that happened.

Events should be immutable where possible.

Listeners should be small and focused.

## Contracts

Use contracts for extension points and replaceable behaviour.

Avoid contracts for everything by default.

Only introduce a contract when there is a clear need for substitution, extension, or external implementation.

## Database

Database changes should remain localized.

Avoid database-specific behaviour when possible.

SQLite and MySQL compatibility are required.

## Future-proofing

Every module should be removable, replaceable, or extendable with minimal impact.
