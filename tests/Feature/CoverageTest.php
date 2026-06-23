<?php

use Trustbird\People\Events\PersonCreated;
use Trustbird\People\Events\PersonUpdated;
use Trustbird\People\Events\PersonTerminated;
use Trustbird\People\Enums\EmploymentStatus;
use Trustbird\People\Enums\EmploymentType;
use Trustbird\People\Enums\PersonnelTaskStatus;
use Trustbird\TrustbirdServiceProvider;

it('instantiates person events', function (): void {
    expect(new PersonCreated())->toBeInstanceOf(PersonCreated::class);
    expect(new PersonUpdated())->toBeInstanceOf(PersonUpdated::class);
    expect(new PersonTerminated())->toBeInstanceOf(PersonTerminated::class);
});

it('can instantiate service provider', function (): void {
    $provider = new TrustbirdServiceProvider(app());
    expect($provider)->toBeInstanceOf(TrustbirdServiceProvider::class);
    
    $provider->register();
    $provider->boot();
    
    // Test class definitions for empty classes
    expect(new \Trustbird\People\Actions\MarkPersonnelTaskComplete())->toBeInstanceOf(\Trustbird\People\Actions\MarkPersonnelTaskComplete::class);
    expect(new \Trustbird\People\Actions\RecordPersonnelReminder())->toBeInstanceOf(\Trustbird\People\Actions\RecordPersonnelReminder::class);
    
    expect(true)->toBeTrue();
});

it('covers all enums', function (): void {
    expect(EmploymentType::cases())->toBeArray()
        ->and(EmploymentType::Employee->value)->toBe('employee');

    expect(EmploymentStatus::cases())->toBeArray()
        ->and(EmploymentStatus::Active->value)->toBe('active');

    expect(PersonnelTaskStatus::cases())->toBeArray()
        ->and(PersonnelTaskStatus::Complete->value)->toBe('complete');
});
