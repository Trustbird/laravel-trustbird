# Trustbird Developer API Guidelines

Trustbird exposes a single, consistent public Developer API.

All interaction with Trustbird domains should happen through the `Trustbird` facade (or the `trustbird()` helper). This API is considered stable and is the only API that should be used in documentation, examples and third-party packages.

Actions, Eloquent models and the service container are implementation details.

---

# Public API

Every domain exposes a typed manager.

```php
use Trustbird\Facades\Trustbird;

$person = Trustbird::people()->create(
    firstName: 'Jane',
    lastName: 'Doe',
    email: 'jane@example.com',
);

Trustbird::people()->terminate($person);
```

or

```php
$person = trustbird()->people()->create(
    firstName: 'Jane',
    lastName: 'Doe',
);

trustbird()->people()->terminate($person);
```

The public API always follows this convention:

```php
Trustbird::{domain}()->{action}(...);
```

Examples:

```php
Trustbird::workspaces()->create(...);
Trustbird::workspaces()->archive($workspace);

Trustbird::people()->create(...);
Trustbird::people()->terminate($person);

Trustbird::assets()->create(...);
Trustbird::assets()->retire($asset);

Trustbird::policies()->publish($policy);

Trustbird::risks()->accept($risk);

Trustbird::controls()->approve($control);
```

---

## Named arguments

Public Trustbird APIs must be designed for PHP named arguments.

Good:

```php
Trustbird::people()->create(
    firstName: 'Jane',
    lastName: 'Doe',
    email: 'jane@example.com',
);
```
Avoid:
```
Trustbird::people()->create([
    'first_name' => 'Jane',
    'last_name' => 'Doe',
    'email' => 'jane@example.com',
]);
```
Rules:

* Use explicit typed parameters for public API methods.
* Use `camelCase` parameter names.
* Avoid associative arrays for public APIs unless the input shape is truly dynamic.
* Avoid vague parameter names like `$data`, `$payload`, `$input` and `$attributes`.
* Required parameters must come before optional parameters.
* Parameter names are part of the public API.
* Renaming a public parameter is a breaking change.
---

# Do not expose implementation details

Direct Eloquent usage is allowed internally where appropriate, but it is not the documented way to perform Trustbird domain operations.

Never use these patterns in public documentation.

Do not expose Actions:

```php
app(CreatePerson::class)->handle(...);
```

Do not expose the service container:

```php
app(SomeService::class)
```

Do not encourage direct Eloquent writes:

```php
Person::create(...);
```

These approaches remain internal implementation details.

---

# Internal architecture

The public manager delegates to one or more Actions or performs simple CRUD directly via Eloquent.

```text
Developer
        │
        ▼
Trustbird::people()->terminate($person)
        │
        ▼
PeopleManager
        │
        ▼
TerminatePerson Action (for complex logic) OR Person model (for simple CRUD)
        │
        ▼
Persistence / Events / Audit Log
```

Actions are responsible for complex business logic.

Managers are responsible for exposing the public API and handling simple CRUD coordination.

---

# Why

This approach provides:

- one consistent API across every domain;
- excellent IDE autocompletion;
- named arguments;
- discoverable methods;
- a stable extension point for plugins;
- freedom to refactor internal architecture.

---

# Custom models

Trustbird must support replacing package models.

Models are resolved through configuration.

```php
'models' => [
    'person' => App\Models\Person::class,
]
```

Managers must never instantiate package models directly.

Instead they resolve the configured model and work against contracts or interfaces.

Because every domain operation goes through the public manager, replacing models does not change the developer experience.

This remains valid regardless of the configured model:

```php
$person = Trustbird::people()->create(...);

Trustbird::people()->terminate($person);
```

---

# Documentation policy
Documentation may show Eloquent models as returned objects, relationships or query results, but must not present Eloquent writes as the recommended way to perform domain behaviour.

Every example in the documentation must use the public Developer API.

Good:

```php
Trustbird::people()->create(...);

Trustbird::people()->terminate($person);
```

Avoid:

```php
Person::create(...);

$person->terminate();

app(CreatePerson::class)->handle(...);
```

---

# Design principle

Trustbird is an application platform, not an Eloquent model library.

Developers should think in terms of domain operations, not model persistence.

The public API defines **what** happens.

The internal Actions define **how** it happens.

This separation allows Trustbird to evolve internally without introducing breaking changes to packages, plugins or applications.