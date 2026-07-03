# Teams

The Teams domain allows you to group people within your organization. Teams can represent departments, squads, projects, or any other internal grouping.

Trustbird is framework-neutral, meaning no teams are added by default. Based on the installed marketplace apps, teams can be suggested during the setup or onboarding process.

## Data model

A team contains:

* Name
* Description
* Owner (Person)
* Members (People)

## Creating a team

```php
use Trustbird\Facades\Trustbird;

$team = Trustbird::teams()->create(
    name: 'Engineering',
    description: 'The software engineering department.',
    ownerId: $person->id,
);
```

## Updating a team

```php
use Trustbird\Facades\Trustbird;

Trustbird::teams()->update($team, [
    'name' => 'Product Engineering',
]);
```

## Deleting a team

```php
use Trustbird\Facades\Trustbird;

Trustbird::teams()->delete($team);
```

## Managing membership

You can add or remove members from a team using the `Teams` manager. These actions support single individuals or groups of people.

### Adding members

```php
use Trustbird\Facades\Trustbird;

// Add a single person by object or ID
Trustbird::teams()->addMember($team, $person);

// Add multiple people at once
Trustbird::teams()->addMember($team, [$person1, $person2]);
```

### Removing members

```php
use Trustbird\Facades\Trustbird;

// Remove a single person
Trustbird::teams()->removeMember($team, $person);

// Remove multiple people
Trustbird::teams()->removeMember($team, [$person1, $person2]);
```

### Checking membership

```php
if ($person->teams->contains($team)) {
    // ...
}
```

## Team ownership

Each team can have an owner, which is a `Person`.

```php
$owner = $team->owner;
$ownedTeams = $person->ownedTeams;
```
