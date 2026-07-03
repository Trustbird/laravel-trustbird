<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Models\Person;
use Trustbird\Teams\Events\MemberAddedToTeam;
use Trustbird\Teams\Events\MemberRemovedFromTeam;
use Trustbird\Teams\Models\Team;

beforeEach(fn () => Event::fake());

it('can create a team via the facade', function () {
    $team = Trustbird::teams()->create(
        name: 'Security HasTeams'
    );

    expect($team)->toBeInstanceOf(Team::class)
        ->and($team->name)->toBe('Security HasTeams');

    Event::assertDispatched('eloquent.created: '.Team::class);
});

it('can update a team via the facade', function () {
    $team = Team::factory()->create(['name' => 'Old HasTeams']);
    $owner = Person::factory()->create();

    $updated = Trustbird::teams()->update(
        $team,
        name: 'New HasTeams',
        description: 'New Description',
        ownerId: $owner->id
    );

    expect($updated->name)->toBe('New HasTeams')
        ->and($updated->description)->toBe('New Description')
        ->and($updated->owner_id)->toBe($owner->id);

    Event::assertDispatched('eloquent.updated: '.Team::class);
});

it('can delete a team via the facade', function () {
    $team = Team::factory()->create();

    $result = Trustbird::teams()->delete($team);

    expect($result)->toBeTrue();
    Event::assertDispatched('eloquent.deleted: '.Team::class);
});

it('can add a member to a team via the facade', function () {
    $team = Team::factory()->create();
    $person = Person::factory()->create();

    Trustbird::teams()->addMember($team, $person);

    expect($team->members)->toHaveCount(1);
    Event::assertDispatched(MemberAddedToTeam::class);
});

it('can remove a member from a team via the facade', function () {
    $team = Team::factory()->create();
    $person = Person::factory()->create();
    $team->members()->attach($person);

    Trustbird::teams()->removeMember($team, $person);

    expect($team->refresh()->members)->toHaveCount(0);
    Event::assertDispatched(MemberRemovedFromTeam::class);
});
