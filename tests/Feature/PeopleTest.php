<?php

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Event;
use Trustbird\Database\Factories\Person\PersonFactory;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Enums\EmploymentStatus;
use Trustbird\People\Enums\EmploymentType;
use Trustbird\People\Events\PersonnelReminderRecorded;
use Trustbird\People\Events\PersonnelTaskMarkedComplete;
use Trustbird\People\Events\PersonTerminated;
use Trustbird\People\Models\Person;

it('creates a person and dispatches event', function (): void {
    Event::fake();

    $person = Trustbird::people()->create(
        firstName: 'Jane',
        lastName: 'Doe',
        email: 'jane@doe.com',
        employmentType: EmploymentType::Employee,
        employmentStatus: EmploymentStatus::Active,
    );

    expect($person)->toBeInstanceOf(Person::class);

    Event::assertDispatched('eloquent.created: '.Person::class);
});

it('updates a person and dispatches event', function (): void {
    Event::fake();

    $person = Person::factory()->create([
        'first_name' => 'Jane',
    ]);

    $updatedPerson = Trustbird::people()->update($person, firstName: 'John');

    expect($updatedPerson->first_name)->toBe('John');

    Event::assertDispatched('eloquent.updated: '.Person::class);
});

it('terminates a person and dispatches event', function (): void {
    Event::fake();

    $person = Person::factory()->create();

    Trustbird::people()->terminate($person);

    expect(
        $person->refresh()->employment_status
    )->toBe(EmploymentStatus::Terminated);

    Event::assertDispatched(PersonTerminated::class, function ($event) use ($person) {
        return $event->person->id === $person->id;
    });
});

it('marks a personnel task complete', function (): void {
    Event::fake();
    $person = Person::factory()->create();

    Trustbird::people()->markTaskComplete($person, task: 'setup');

    Event::assertDispatched(PersonnelTaskMarkedComplete::class, function ($event) use ($person) {
        return $event->person->id === $person->id && $event->taskData['task'] === 'setup';
    });
});

it('records a personnel reminder', function (): void {
    Event::fake();
    $person = Person::factory()->create();

    Trustbird::people()->recordReminder($person, type: 'contract', remindAt: now());

    Event::assertDispatched(PersonnelReminderRecorded::class, function ($event) use ($person) {
        return $event->person->id === $person->id && $event->reminderData['type'] === 'contract';
    });
});

it('can access the person factory', function (): void {
    $factory = Person::newFactory();
    expect($factory)->toBeInstanceOf(PersonFactory::class);
});

it('covers person casts', function (): void {
    $person = Person::factory()->create([
        'metadata' => ['key' => 'value'],
        'started_at' => now(),
    ]);

    expect($person->metadata)->toBeArray()
        ->and($person->metadata['key'])->toBe('value')
        ->and($person->started_at)->toBeInstanceOf(CarbonInterface::class);
});
