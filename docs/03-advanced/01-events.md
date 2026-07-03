# Events

Trustbird uses events to allow applications to react to changes within the system.

## Event Strategy

Trustbird follows two strategies for events:

1.  **Standard CRUD events**: For basic Create, Update, and Delete operations, Trustbird relies on Laravel's built-in Eloquent lifecycle events.
2.  **Semantic Domain events**: For specific business operations that carry more meaning than a simple database update, Trustbird dispatches dedicated event classes.

## Listening to CRUD Events

You can listen to standard Eloquent events for any Trustbird model. These events follow the Laravel convention: `eloquent.{event}: {ModelClass}`.

Example of listening to a person being created:

```php
use Trustbird\People\Models\Person;
use Illuminate\Support\Facades\Event;

Event::listen("eloquent.created: " . Person::class, function (Person $person) {
    // Handle new person
});
```

Commonly used Eloquent events:

*   `eloquent.created: Model::class`
*   `eloquent.updated: Model::class`
*   `eloquent.deleted: Model::class`

## Semantic Domain Events

The following dedicated events are available across different domains:

### People Domain

*   `Trustbird\People\Events\PersonTerminated`: Dispatched when a person's employment is terminated.
*   `Trustbird\People\Events\PersonnelTaskMarkedComplete`: Dispatched when a task for a person is marked as complete.
*   `Trustbird\People\Events\PersonnelReminderRecorded`: Dispatched when a reminder for a person is recorded.

### Teams Domain

*   `Trustbird\Teams\Events\MemberAddedToTeam`: Dispatched when one or more members are added to a team.
*   `Trustbird\Teams\Events\MemberRemovedFromTeam`: Dispatched when one or more members are removed from a team.

### Risks Domain

*   `Trustbird\Risks\Events\RiskReviewed`: Dispatched when a risk review is recorded.

## Examples

### Registering a Listener

You can register listeners in your `AppServiceProvider` or a dedicated `EventServiceProvider`:

```php
use Trustbird\People\Events\PersonTerminated;
use App\Listeners\NotifySecurityDept;
use Illuminate\Support\Facades\Event;

Event::listen(
    PersonTerminated::class,
    NotifySecurityDept::class,
);
```
