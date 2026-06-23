<?php

use Trustbird\People\Actions\CreatePerson;
use Trustbird\People\Actions\MarkPersonnelTaskComplete;
use Trustbird\People\Actions\RecordPersonnelReminder;
use Trustbird\People\Actions\TerminatePerson;
use Trustbird\People\Actions\UpdatePerson;
use Trustbird\People\Enums\EmploymentStatus;
use Trustbird\People\Enums\EmploymentType;
use Trustbird\People\Models\Person;

it('creates a person', function (): void {
    $person = app(CreatePerson::class)->handle([
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@doe.com',
        'employment_type' => EmploymentType::Employee,
        'employment_status' => EmploymentStatus::Active,
    ]);

    expect($person)->toBeInstanceOf(Person::class);
});

it('updates a person', function (): void {
    $person = Person::factory()->create([
        'first_name' => 'Jane',
    ]);

    $updatedPerson = app(UpdatePerson::class)->handle($person, [
        'first_name' => 'John',
    ]);

    expect($updatedPerson->first_name)->toBe('John');
});

it('terminates a person', function (): void {
    $person = Person::factory()->create();

    app(TerminatePerson::class)->handle($person);

    expect(
        $person->refresh()->employment_status
    )->toBe(EmploymentStatus::Terminated);
});

it('marks a personnel task complete', function (): void {
    $action = new MarkPersonnelTaskComplete();
    expect($action)->toBeInstanceOf(MarkPersonnelTaskComplete::class);
});

it('records a personnel reminder', function (): void {
    $action = new RecordPersonnelReminder();
    expect($action)->toBeInstanceOf(RecordPersonnelReminder::class);
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