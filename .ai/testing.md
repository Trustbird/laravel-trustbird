# Testing

Testing is mandatory.

This package has a strict 100% test coverage policy.

No code may be merged unless test coverage remains 100%.

## Framework

Use:

- PestPHP
- Laravel Pint
- Laravel testing utilities

## Code quality

Before you run any tests, you should run:

`./vendor/bin/pint --dirty --parallel`

## Coverage policy

The required coverage is:

100%

This applies to:

- lines
- branches where measured
- meaningful public behaviour

Do not reduce coverage.

Do not exclude code from coverage unless there is a clear technical reason and the exclusion is explicitly justified.

## Feature development

Every new feature requires tests.

New features should include:

- feature tests for externally visible behaviour
- unit tests for isolated domain logic where appropriate
- regression tests for previously reported bugs

## Bug fixes

Bug fixes require:

1. a failing test that reproduces the bug
2. the implementation fix
3. a passing test suite

## Compatibility

Tests must run on:

- SQLite
- MySQL

Avoid database-specific behaviour.

When database-specific behaviour is unavoidable, document it and cover it with tests.

## Test quality

Tests should be:

- readable
- deterministic
- isolated
- fast
- focused on behaviour

Avoid testing implementation details unless necessary.

## Factories

Use factories for test data whenever possible.

Avoid manually creating large object graphs inside tests.

## Events

When testing events:

- For standard CRUD operations (create, update, delete), assert on the built-in Eloquent events:
  ```php
  Event::assertDispatched("eloquent.created: " . Person::class);
  ```
- For semantic domain events (e.g. `PersonTerminated`), assert on the specific event class:
  ```php
  Event::assertDispatched(PersonTerminated::class);
  ```

## Confidence rule

Every pull request should increase confidence in the package.

If a change is hard to test, reconsider the design.
