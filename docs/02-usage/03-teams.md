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
use Trustbird\Teams\Actions\CreateTeam;

$team = app(CreateTeam::class)->handle([
    'name' => 'Engineering',
    'description' => 'The software engineering department.',
    'owner_id' => $person->id,
]);
```

## Updating a team

```php
use Trustbird\Teams\Actions\UpdateTeam;

app(UpdateTeam::class)->handle($team, [
    'name' => 'Product Engineering',
]);
```

## Deleting a team

```php
use Trustbird\Teams\Actions\DeleteTeam;

app(DeleteTeam::class)->handle($team);
```

## Managing membership

You can add or remove members from a team using dedicated actions. These actions support single individuals or groups of people.

### Adding members

```php
use Trustbird\Teams\Actions\AddMemberToTeam;

// Add a single person by object or ID
app(AddMemberToTeam::class)->handle($team, $person);

// Add multiple people at once
app(AddMemberToTeam::class)->handle($team, [$person1, $person2]);
```

### Removing members

```php
use Trustbird\Teams\Actions\RemoveMemberFromTeam;

// Remove a single person
app(RemoveMemberFromTeam::class)->handle($team, $person);

// Remove multiple people
app(RemoveMemberFromTeam::class)->handle($team, [$person1, $person2]);
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
