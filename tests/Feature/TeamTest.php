<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Database\Factories\Team\TeamFactory;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Models\Person;
use Trustbird\Teams\Events\MemberAddedToTeam;
use Trustbird\Teams\Events\MemberRemovedFromTeam;
use Trustbird\Teams\Models\Team;
use Trustbird\Workspaces\Models\Workspace;

it('creates a team and dispatches event', function (): void {
    Event::fake();
    $workspace = Workspace::factory()->create();
    $owner = Person::factory()->create(['workspace_id' => $workspace->id]);

    $team = Trustbird::teams()->create(
        name: 'Engineering',
        description: 'Software engineering team',
        ownerId: $owner->id,
        workspaceId: $workspace->id,
    );

    expect($team)->toBeInstanceOf(Team::class)
        ->and($team->name)->toBe('Engineering')
        ->and($team->owner_id)->toBe($owner->id);

    Event::assertDispatched('eloquent.created: '.Team::class);
});

it('updates a team and dispatches event', function (): void {
    Event::fake();
    $team = Team::factory()->create(['name' => 'Old Name']);

    $updatedTeam = Trustbird::teams()->update($team, name: 'New Name');

    expect($updatedTeam->name)->toBe('New Name');

    Event::assertDispatched('eloquent.updated: '.Team::class);
});

it('can have members', function (): void {
    $team = Team::factory()->create();
    $person1 = Person::factory()->create(['workspace_id' => $team->workspace_id]);
    $person2 = Person::factory()->create(['workspace_id' => $team->workspace_id]);

    $team->members()->attach([$person1->id, $person2->id]);

    expect($team->members)->toHaveCount(2)
        ->and($team->members->pluck('id'))->toContain($person1->id, $person2->id);

    expect($person1->teams)->toHaveCount(1)
        ->and($person1->teams->first()->id)->toBe($team->id);
});

it('can have an owner', function (): void {
    $person = Person::factory()->create();
    $team = Team::factory()->create(['owner_id' => $person->id]);

    expect($team->owner->id)->toBe($person->id);
    expect($person->ownedTeams)->toHaveCount(1)
        ->and($person->ownedTeams->first()->id)->toBe($team->id);
});

it('automatically assigns the first workspace in single tenant mode', function () {
    config(['trustbird.multi_tenant' => false]);
    $workspace = Workspace::factory()->create();

    $team = Team::create([
        'name' => 'Marketing',
    ]);

    expect($team->workspace_id)->toBe($workspace->id);
});

it('deletes a team and dispatches event without deleting members', function (): void {
    Event::fake();
    $team = Team::factory()->create();
    $person = Person::factory()->create(['workspace_id' => $team->workspace_id]);
    $team->members()->attach($person->id);

    $deleted = Trustbird::teams()->delete($team);

    expect($deleted)->toBeTrue();
    expect(Team::find($team->id))->toBeNull();

    // Verifieer dat de persoon nog steeds bestaat
    expect(Person::find($person->id))->not->toBeNull();

    Event::assertDispatched('eloquent.deleted: '.Team::class);
});

it('adds members to a team and dispatches event', function (): void {
    Event::fake();
    $team = Team::factory()->create();
    $person1 = Person::factory()->create(['workspace_id' => $team->workspace_id]);
    $person2 = Person::factory()->create(['workspace_id' => $team->workspace_id]);

    // Test toevoegen van één persoon (object)
    Trustbird::teams()->addMember($team, $person1);

    expect($team->refresh()->members)->toHaveCount(1)
        ->and($team->members->first()->id)->toBe($person1->id);

    Event::assertDispatched(MemberAddedToTeam::class, function ($event) use ($team, $person1) {
        return $event->team->id === $team->id && in_array($person1->id, $event->personIds);
    });

    // Test toevoegen van meerdere personen (array van ID's)
    Trustbird::teams()->addMember($team, [$person2->id]);

    expect($team->refresh()->members)->toHaveCount(2)
        ->and($team->members->pluck('id'))->toContain($person1->id, $person2->id);
});

it('removes members from a team and dispatches event', function (): void {
    Event::fake();
    $team = Team::factory()->create();
    $person1 = Person::factory()->create(['workspace_id' => $team->workspace_id]);
    $person2 = Person::factory()->create(['workspace_id' => $team->workspace_id]);
    $team->members()->attach([$person1->id, $person2->id]);

    // Test verwijderen van één persoon (string ID)
    Trustbird::teams()->removeMember($team, $person1->id);

    expect($team->refresh()->members)->toHaveCount(1)
        ->and($team->members->first()->id)->toBe($person2->id);

    Event::assertDispatched(MemberRemovedFromTeam::class, function ($event) use ($team, $person1) {
        return $event->team->id === $team->id && in_array($person1->id, $event->personIds);
    });

    // Test verwijderen van meerdere personen (array van objecten)
    Trustbird::teams()->removeMember($team, [$person2]);

    expect($team->refresh()->members)->toBeEmpty();
});

it('can access the team factory', function (): void {
    $factory = Team::newFactory();
    expect($factory)->toBeInstanceOf(TeamFactory::class);
});
