<?php

use Trustbird\People\Events\PersonCreated;
use Trustbird\People\Events\PersonUpdated;
use Trustbird\People\Events\PersonTerminated;
use Trustbird\Assets\Events\AssetCreated;
use Trustbird\Assets\Events\AssetUpdated;
use Trustbird\Assets\Events\AssetDeleted;
use Trustbird\Assets\Enums\AssetKind;
use Trustbird\Risks\Events\RiskCreated;
use Trustbird\Risks\Events\RiskUpdated;
use Trustbird\Risks\Events\RiskReviewed;
use Trustbird\Risks\Enums\RiskStatus;
use Trustbird\Risks\Enums\RiskTreatment;
use Trustbird\Risks\Enums\RiskLevel;
use Trustbird\Risks\Models\Risk;
use Trustbird\Policies\Events\PolicyCreated;
use Trustbird\Policies\Events\PolicyUpdated;
use Trustbird\Policies\Events\PolicyReviewed;
use Trustbird\Policies\Events\PolicyVersionDrafted;
use Trustbird\Policies\Events\PolicyVersionUpdated;
use Trustbird\Policies\Events\PolicyVersionPublished;
use Trustbird\Policies\Enums\PolicyVersionStatus;
use Trustbird\Policies\Models\Policy;
use Trustbird\People\Enums\EmploymentStatus;
use Trustbird\People\Enums\EmploymentType;
use Trustbird\People\Enums\PersonnelTaskStatus;
use Trustbird\TrustbirdServiceProvider;
use Trustbird\People\Models\Person;
use Trustbird\Assets\Models\Asset;
use Trustbird\Workspaces\Models\Workspace;
use Trustbird\Workspaces\Events\WorkspaceCreated;
use Trustbird\Workspaces\Events\WorkspaceUpdated;

it('instantiates events', function (): void {
    $person = Person::factory()->create();
    $asset = Asset::factory()->create();

    expect(new PersonCreated($person))->toBeInstanceOf(PersonCreated::class);
    expect(new PersonUpdated($person))->toBeInstanceOf(PersonUpdated::class);
    expect(new PersonTerminated($person))->toBeInstanceOf(PersonTerminated::class);
    
    expect(new AssetCreated($asset))->toBeInstanceOf(AssetCreated::class);
    expect(new AssetUpdated($asset))->toBeInstanceOf(AssetUpdated::class);
    expect(new AssetDeleted($asset))->toBeInstanceOf(AssetDeleted::class);

    $risk = Risk::factory()->create();
    expect(new RiskCreated($risk))->toBeInstanceOf(RiskCreated::class);
    expect(new RiskUpdated($risk))->toBeInstanceOf(RiskUpdated::class);
    expect(new RiskReviewed($risk))->toBeInstanceOf(RiskReviewed::class);

    $policy = Policy::factory()->withDraftVersion()->create();
    $version = $policy->versions->first();
    expect(new PolicyCreated($policy))->toBeInstanceOf(PolicyCreated::class);
    expect(new PolicyUpdated($policy))->toBeInstanceOf(PolicyUpdated::class);
    expect(new PolicyReviewed($policy))->toBeInstanceOf(PolicyReviewed::class);
    expect(new PolicyVersionDrafted($policy, $version))->toBeInstanceOf(PolicyVersionDrafted::class);
    expect(new PolicyVersionUpdated($version))->toBeInstanceOf(PolicyVersionUpdated::class);
    expect(new PolicyVersionPublished($policy, $version))->toBeInstanceOf(PolicyVersionPublished::class);

    $workspace = Workspace::factory()->create();
    expect(new WorkspaceCreated($workspace))->toBeInstanceOf(WorkspaceCreated::class);
    expect(new WorkspaceUpdated($workspace))->toBeInstanceOf(WorkspaceUpdated::class);
});

it('can instantiate service provider', function (): void {
    $provider = new TrustbirdServiceProvider(app());
    expect($provider)->toBeInstanceOf(TrustbirdServiceProvider::class);
    
    $provider->register();
    $provider->boot();
    
    // Test class definitions for empty classes
    expect(new \Trustbird\People\Actions\MarkPersonnelTaskComplete())->toBeInstanceOf(\Trustbird\People\Actions\MarkPersonnelTaskComplete::class);
    expect(new \Trustbird\People\Actions\RecordPersonnelReminder())->toBeInstanceOf(\Trustbird\People\Actions\RecordPersonnelReminder::class);
    
    // Test new event constructors
    $person = \Trustbird\People\Models\Person::factory()->make();
    expect(new \Trustbird\People\Events\PersonnelTaskMarkedComplete($person, ['test' => 1]))->toBeInstanceOf(\Trustbird\People\Events\PersonnelTaskMarkedComplete::class);
    expect(new \Trustbird\People\Events\PersonnelReminderRecorded($person, ['test' => 1]))->toBeInstanceOf(\Trustbird\People\Events\PersonnelReminderRecorded::class);
    
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
});
