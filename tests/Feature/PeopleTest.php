<?php

use Trustbird\People\Actions\CreatePerson;
use Trustbird\People\Actions\MarkPersonnelTaskComplete;
use Trustbird\People\Actions\RecordPersonnelReminder;
use Trustbird\People\Actions\TerminatePerson;
use Trustbird\People\Actions\UpdatePerson;
use Trustbird\People\Enums\EmploymentStatus;
use Trustbird\People\Enums\EmploymentType;
use Trustbird\People\Events\PersonCreated;
use Trustbird\People\Events\PersonTerminated;
use Trustbird\People\Events\PersonUpdated;
use Trustbird\People\Models\Person;
use Illuminate\Support\Facades\Event;

it('creates a person and dispatches event', function (): void {
    Event::fake();

    $person = app(CreatePerson::class)->handle([
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@doe.com',
        'employment_type' => EmploymentType::Employee,
        'employment_status' => EmploymentStatus::Active,
    ]);

    expect($person)->toBeInstanceOf(Person::class);
    
    Event::assertDispatched(PersonCreated::class, function ($event) use ($person) {
        return $event->person->id === $person->id;
    });
});

it('updates a person and dispatches event', function (): void {
    Event::fake();

    $person = Person::factory()->create([
        'first_name' => 'Jane',
    ]);

    $updatedPerson = app(UpdatePerson::class)->handle($person, [
        'first_name' => 'John',
    ]);

    expect($updatedPerson->first_name)->toBe('John');
    
    Event::assertDispatched(PersonUpdated::class, function ($event) use ($person) {
        return $event->person->id === $person->id;
    });
});

it('terminates a person and dispatches event', function (): void {
    Event::fake();

    $person = Person::factory()->create();

    app(TerminatePerson::class)->handle($person);

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
    
    $action = new MarkPersonnelTaskComplete();
    $action->handle($person, ['task' => 'setup']);
    
    Event::assertDispatched(\Trustbird\People\Events\PersonnelTaskMarkedComplete::class, function ($event) use ($person) {
        return $event->person->id === $person->id && $event->taskData['task'] === 'setup';
    });
});

it('records a personnel reminder', function (): void {
    Event::fake();
    $person = Person::factory()->create();
    
    $action = new RecordPersonnelReminder();
    $action->handle($person, ['reminder' => 'contract']);
    
    Event::assertDispatched(\Trustbird\People\Events\PersonnelReminderRecorded::class, function ($event) use ($person) {
        return $event->person->id === $person->id && $event->reminderData['reminder'] === 'contract';
    });
});

it('can access the person factory', function (): void {
    $factory = Person::newFactory();
    expect($factory)->toBeInstanceOf(\Trustbird\Database\Factories\Person\PersonFactory::class);
});

it('covers person casts', function (): void {
    $person = Person::factory()->create([
        'metadata' => ['key' => 'value'],
        'started_at' => now(),
    ]);
    
    expect($person->metadata)->toBeArray()
        ->and($person->metadata['key'])->toBe('value')
        ->and($person->started_at)->toBeInstanceOf(\Carbon\CarbonInterface::class);
});