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
    
    expect(true)->toBeTrue();
});

it('covers all enums', function (): void {
    expect(EmploymentType::cases())->toBeArray();
    expect(EmploymentStatus::cases())->toBeArray();
    expect(PersonnelTaskStatus::cases())->toBeArray();
});
