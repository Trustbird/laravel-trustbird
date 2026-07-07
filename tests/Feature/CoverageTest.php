<?php

use Trustbird\Assets\Enums\AssetKind;
use Trustbird\People\Actions\MarkPersonnelTaskComplete;
use Trustbird\People\Actions\RecordPersonnelReminder;
use Trustbird\Risks\Events\RiskReviewed;
use Trustbird\Risks\Enums\RiskStatus;
use Trustbird\Risks\Enums\RiskTreatment;
use Trustbird\Risks\Enums\RiskLevel;
use Trustbird\Risks\Models\Risk;
use Trustbird\Policies\Events\PolicyReviewed;
use Trustbird\Policies\Events\PolicyVersionDrafted;
use Trustbird\Policies\Events\PolicyVersionUpdated;
use Trustbird\Policies\Events\PolicyVersionPublished;
use Trustbird\Policies\Enums\PolicyVersionStatus;
use Trustbird\Policies\Models\Policy;
use Trustbird\Incidents\Enums\IncidentSeverity;
use Trustbird\Incidents\Enums\IncidentStatus;
use Trustbird\People\Enums\EmploymentStatus;
use Trustbird\People\Enums\EmploymentType;
use Trustbird\People\Enums\PersonnelTaskStatus;
use Trustbird\People\Events\PersonnelReminderRecorded;
use Trustbird\People\Events\PersonnelTaskMarkedComplete;
use Trustbird\People\Events\PersonTerminated;
use Trustbird\People\Models\Person;
use Trustbird\TrustbirdServiceProvider;

it('instantiates events', function (): void {
    $person = Person::factory()->create();

    expect(new PersonTerminated($person))->toBeInstanceOf(PersonTerminated::class);

    $risk = Risk::factory()->create();
    expect(new RiskReviewed($risk))->toBeInstanceOf(RiskReviewed::class);

    $policy = Policy::factory()->withDraftVersion()->create();
    $version = $policy->versions->first();

    expect(new PolicyReviewed($policy))->toBeInstanceOf(PolicyReviewed::class);
    expect(new PolicyVersionDrafted($policy, $version))->toBeInstanceOf(PolicyVersionDrafted::class);
    expect(new PolicyVersionUpdated($version))->toBeInstanceOf(PolicyVersionUpdated::class);
    expect(new PolicyVersionPublished($policy, $version))->toBeInstanceOf(PolicyVersionPublished::class);
});

it('can instantiate service provider', function (): void {
    $provider = new TrustbirdServiceProvider(app());
    expect($provider)->toBeInstanceOf(TrustbirdServiceProvider::class);

    $provider->register();
    $provider->boot();

    // Test class definitions for empty classes
    expect(new MarkPersonnelTaskComplete)->toBeInstanceOf(MarkPersonnelTaskComplete::class);
    expect(new RecordPersonnelReminder)->toBeInstanceOf(RecordPersonnelReminder::class);

    // Test new event constructors
    $person = Person::factory()->make();
    expect(new PersonnelTaskMarkedComplete($person, ['test' => 1]))->toBeInstanceOf(PersonnelTaskMarkedComplete::class);
    expect(new PersonnelReminderRecorded($person, ['test' => 1]))->toBeInstanceOf(PersonnelReminderRecorded::class);

    expect(true)->toBeTrue();
});

it('covers all enums', function (): void {
    expect(EmploymentType::cases())->toBeArray()
        ->and(EmploymentType::Employee->value)->toBe('employee');

    expect(EmploymentStatus::cases())->toBeArray()
        ->and(EmploymentStatus::Active->value)->toBe('active');

    expect(PersonnelTaskStatus::cases())->toBeArray()
        ->and(PersonnelTaskStatus::Complete->value)->toBe('complete');

    expect(AssetKind::cases())->toBeArray()
        ->and(AssetKind::Device->value)->toBe('device');

    expect(RiskStatus::cases())->toBeArray()
        ->and(RiskStatus::Open->value)->toBe('open');

    expect(RiskTreatment::cases())->toBeArray()
        ->and(RiskTreatment::Reduce->value)->toBe('reduce');

    expect(RiskLevel::cases())->toBeArray()
        ->and(RiskLevel::High->value)->toBe('high');

    expect(PolicyVersionStatus::cases())->toBeArray()
        ->and(PolicyVersionStatus::Draft->value)->toBe('draft');

    expect(IncidentSeverity::cases())->toBeArray()
        ->and(IncidentSeverity::Critical->value)->toBe('critical');

    expect(IncidentStatus::cases())->toBeArray()
        ->and(IncidentStatus::Open->value)->toBe('open');
});
