# Architecture

This package is modular, backend-only, and Laravel-native.

Business logic should remain isolated, testable, and independent from presentation concerns.

## Layers

The preferred dependency flow is:
```text
Developer API / Application entry points
↓
Typed managers
↓
Actions (for complex logic) OR Domain Models (for simple CRUD)
↓
Persistence
```
The public Developer API is defined in `.ai/developer-api.md`.

Typed managers expose stable domain operations through the `Trustbird` facade or the `trustbird()` helper. Simple CRUD operations (Create, Update, Delete) are handled directly by Managers using Eloquent models. Actions are reserved for more complex domain operations that involve business logic, side effects, or multiple steps.

## Business logic

Business logic never belongs in:

- controllers
- commands
- service providers
- models
- API resources
- validation rules

Business logic belongs in:

- actions (for operations spanning multiple steps or side effects)
- typed managers (for coordination and simple orchestration)
- services
- domain objects
- value objects

## Models

Models represent persistence.

Models may contain relationships, casts, scopes, and simple accessors.

Models must not become business logic containers.

## Actions

Actions perform complex business operations.

One action performs one task.

Actions should be easy to test directly.

Simple CRUD operations do not require dedicated Action classes; Managers handle these directly via Eloquent. Every domain operation must trigger a corresponding Event. Standard CRUD operations rely on Laravel's built-in Eloquent events, while semantic domain events are explicitly dispatched by Actions.

## Services

Services coordinate behaviour that spans multiple actions or external concerns.

Services should not become god objects.

## Events

Events should describe something that happened.

Events should be immutable where possible.

Listeners should be small and focused.

## Contracts

Use contracts for extension points and replaceable behaviour.

To avoid naming conflicts with concrete models, interfaces for domain models follow the `Has{Model}s` (plural) convention (e.g., `HasPeople`, `HasAssets`).

Avoid contracts for everything by default.

Only introduce a contract when there is a clear need for substitution, extension, or external implementation.

## Concerns

Traits that provide domain functionality to models follow the `InteractsWith{Model}s` (plural) convention (e.g., `InteractsWithPeople`, `InteractsWithAssets`).

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
