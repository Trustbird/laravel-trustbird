<?php

use Trustbird\People\Actions\CreatePerson;
use Trustbird\People\Actions\TerminatePerson;
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

it('terminates a person', function (): void {
    $person = Person::factory()->create();

    app(TerminatePerson::class)->handle($person);

    expect(
        $person->refresh()->employment_status
    )->toBe(EmploymentStatus::Terminated);
});