<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Enums\EmploymentStatus;
use Trustbird\People\Enums\EmploymentType;
use Trustbird\People\Events\PersonnelReminderRecorded;
use Trustbird\People\Events\PersonnelTaskMarkedComplete;
use Trustbird\People\Events\PersonTerminated;
use Trustbird\People\Models\Person;

beforeEach(fn () => Event::fake());

it('can create a person via the facade', function () {
    $person = Trustbird::people()->create(
        firstName: 'Jane',
        lastName: 'Doe',
        email: 'jane@example.com'
    );

    expect($person)->toBeInstanceOf(Person::class)
        ->and($person->first_name)->toBe('Jane')
        ->and($person->last_name)->toBe('Doe')
        ->and($person->email)->toBe('jane@example.com');

    Event::assertDispatched('eloquent.created: '.Person::class);
});

it('can update a person via the facade', function () {
    $person = Person::factory()->create(['first_name' => 'Old']);
    $startedAt = now()->subYear();
    $endedAt = now();

    $updated = Trustbird::people()->update(
        $person,
        firstName: 'New',
        lastName: 'Doe',
        email: 'new@example.com',
        employmentType: EmploymentType::Contractor,
        employmentStatus: EmploymentStatus::Offboarding,
        startedAt: $startedAt,
        endedAt: $endedAt,
        metadata: ['key' => 'value']
    );

    expect($updated->first_name)->toBe('New')
        ->and($updated->last_name)->toBe('Doe')
        ->and($updated->email)->toBe('new@example.com')
        ->and($updated->employment_type)->toBe(EmploymentType::Contractor)
        ->and($updated->employment_status)->toBe(EmploymentStatus::Offboarding)
        ->and($updated->started_at->format('Y-m-d'))->toBe($startedAt->format('Y-m-d'))
        ->and($updated->ended_at->format('Y-m-d'))->toBe($endedAt->format('Y-m-d'))
        ->and($updated->metadata)->toBe(['key' => 'value']);

    Event::assertDispatched('eloquent.updated: '.Person::class);
});

it('can terminate a person via the facade', function () {
    $person = Person::factory()->create();

    $terminated = Trustbird::people()->terminate($person);

    expect($terminated->employment_status)->toBe(EmploymentStatus::Terminated);
    Event::assertDispatched(PersonTerminated::class);
});

it('can record a reminder via the facade', function () {
    $person = Person::factory()->create();

    Trustbird::people()->recordReminder($person, type: 'test', remindAt: now());

    Event::assertDispatched(PersonnelReminderRecorded::class);
});

it('can mark a task complete via the facade', function () {
    $person = Person::factory()->create();

    Trustbird::people()->markTaskComplete($person, task: 'test');

    Event::assertDispatched(PersonnelTaskMarkedComplete::class);
});
