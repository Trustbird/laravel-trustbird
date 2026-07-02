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

## Multi-tenancy

Trustbird is a multi-tenant application by design, but supports both single-tenant and multi-tenant deployments.

### Configuration

Multi-tenancy behavior is controlled via the `trustbird.multi_tenant` configuration key:

- `false` (default): Single-tenant mode. A default workspace is used if none is provided.
- `true`: Multi-tenant mode. A `workspace_id` must be explicitly provided during resource creation.

### Resource Isolation

Every resource must belong to a `Workspace`.

- All models (except `Workspace` itself) must include a `workspace_id` foreign key.
- All models must use the `Trustbird\Workspaces\Concerns\BelongsToWorkspace` trait. This trait:
    - Automatically adds `workspace_id` to the model's `$fillable` attributes.
    - In single-tenant mode, automatically assigns the first available workspace if `workspace_id` is missing.
    - In multi-tenant mode, throws a `RuntimeException` if `workspace_id` is missing during creation.
- All database migrations must include a `workspace_id` column:
  ```php
  $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();
  ```
- All factory definitions must include a `workspace_id` associated with a `Workspace` factory.

### Installation

During installation, a default workspace should be created. This can be done using the provided command:

```bash
php artisan trustbird:install
```

## Future-proofing

Every module should be removable, replaceable, or extendable with minimal impact.
