# Coding Style

## General

Prefer:

- simple code
- descriptive names
- composition
- immutable value objects
- small classes
- explicit dependencies
- Laravel conventions

Avoid:

- inheritance-heavy designs
- static state
- god classes
- deep nesting
- hidden side effects
- unnecessary abstractions

## PHP

Use:

- strict types
- typed properties
- explicit return types
- constructor property promotion where useful
- named constructors for complex value objects

Avoid:

- mixed return types unless unavoidable
- untyped arrays for structured data
- magic strings
- magic numbers

## Actions

Actions should have one clear responsibility.

Action names should describe the operation.

Examples:

- `CreatePerson`
- `TerminatePerson`
- `CreatePolicy`
- `ReviewEvidence`

## Exceptions

Throw meaningful exceptions.

Never silently ignore failures.

Exception messages should be useful for developers.

## Configuration

Configuration belongs in:

`config/trustbird.php`

Never hardcode configurable values.

## Documentation

Public classes, methods, configuration options, and extension points should be documented when their purpose is not immediately obvious.
