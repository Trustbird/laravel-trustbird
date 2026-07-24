<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Controls\Models\Control;
use Trustbird\Facades\Trustbird;
use Trustbird\Frameworks\Enums\FrameworkMappingCoverage;
use Trustbird\Frameworks\Enums\FrameworkVersionStatus;
use Trustbird\Frameworks\Events\FrameworkVersionDrafted;
use Trustbird\Frameworks\Events\FrameworkVersionPublished;
use Trustbird\Frameworks\Models\Framework;
use Trustbird\Frameworks\Models\FrameworkMapping;
use Trustbird\Frameworks\Models\FrameworkRequirement;
use Trustbird\Frameworks\Models\FrameworkVersion;
use Trustbird\People\Models\Person;

beforeEach(fn () => Event::fake());

test('it can create a framework with an initial draft version', function (): void {
    $owner = Person::factory()->create();

    $framework = Trustbird::frameworks()->create(
        name: 'Information security readiness',
        description: 'Trustbird-owned readiness checklist for small organisations.',
        slug: 'info-sec-readiness',
        ownerId: $owner->id,
        versionLabel: '1.0',
        changeSummary: 'Initial draft',
    );

    expect($framework)->toBeInstanceOf(Framework::class)
        ->name->toBe('Information security readiness')
        ->owner_id->toBe($owner->id)
        ->hasPublishedVersion()->toBeFalse();

    expect($framework->versions)->toHaveCount(1);
    expect($framework->versions->first())
        ->version_label->toBe('1.0')
        ->status->toBe(FrameworkVersionStatus::Draft);

    Event::assertDispatched(FrameworkVersionDrafted::class);
});

test('it can update a framework', function (): void {
    $framework = Framework::factory()->create(['name' => 'Old name']);

    $updated = Trustbird::frameworks()->update(
        framework: $framework,
        name: 'Updated readiness guide',
    );

    expect($updated->name)->toBe('Updated readiness guide');
});

test('it can publish a framework version and dispatches event', function (): void {
    $framework = Framework::factory()->withDraftVersion()->create();
    $version = $framework->versions->first();
    $publisher = Person::factory()->create(['workspace_id' => $framework->workspace_id]);

    $published = Trustbird::frameworks()->publishVersion(
        framework: $framework,
        version: $version,
        publishedById: $publisher->id,
    );

    expect($published->status)->toBe(FrameworkVersionStatus::Published)
        ->and($framework->fresh()->current_version_id)->toBe($version->id)
        ->and($framework->fresh()->hasPublishedVersion())->toBeTrue();

    Event::assertDispatched(FrameworkVersionPublished::class);
});

test('it cannot publish a version that does not belong to the framework', function (): void {
    $framework = Framework::factory()->withDraftVersion()->create();
    $otherVersion = FrameworkVersion::factory()->create();

    Trustbird::frameworks()->publishVersion(
        framework: $framework,
        version: $otherVersion,
    );
})->throws(InvalidArgumentException::class, 'The framework version does not belong to this framework.');

test('it cannot publish a non-draft framework version', function (): void {
    $framework = Framework::factory()->withPublishedVersion()->create();
    $version = $framework->currentVersion;

    Trustbird::frameworks()->publishVersion(
        framework: $framework,
        version: $version,
    );
})->throws(InvalidArgumentException::class, 'Only draft framework versions can be published.');

test('it supersedes the previous published version when publishing a new one', function (): void {
    $framework = Framework::factory()->withPublishedVersion()->create();
    $firstVersion = $framework->currentVersion;

    $draft = Trustbird::frameworks()->draftVersion(
        framework: $framework,
        versionLabel: '2.0',
        changeSummary: 'Annual refresh',
    );

    Trustbird::frameworks()->publishVersion(
        framework: $framework,
        version: $draft,
    );

    expect($firstVersion->fresh()->status)->toBe(FrameworkVersionStatus::Superseded)
        ->and($framework->fresh()->current_version_id)->toBe($draft->id);
});

test('it can add and update requirements in Trustbird-owned language', function (): void {
    $framework = Framework::factory()->withDraftVersion()->create();
    $version = $framework->versions->first();

    $requirement = Trustbird::frameworks()->addRequirement(
        version: $version,
        title: 'Know who can access sensitive systems',
        code: 'ACCESS-1',
        summary: 'Keep a clear list of people with privileged access.',
        position: 1,
    );

    expect($requirement)->toBeInstanceOf(FrameworkRequirement::class)
        ->title->toBe('Know who can access sensitive systems')
        ->code->toBe('ACCESS-1');

    $updated = Trustbird::frameworks()->updateRequirement(
        requirement: $requirement,
        summary: 'Document privileged access and review it regularly.',
    );

    expect($updated->summary)->toBe('Document privileged access and review it regularly.');
});

test('it can map a requirement to a canonical Trustbird object', function (): void {
    $framework = Framework::factory()->withDraftVersion()->create();
    $version = $framework->versions->first();
    $requirement = Trustbird::frameworks()->addRequirement(
        version: $version,
        title: 'Encrypt portable devices',
    );
    $control = Control::factory()->create(['workspace_id' => $framework->workspace_id]);

    $mapping = Trustbird::frameworks()->map(
        requirement: $requirement,
        related: $control,
        coverage: FrameworkMappingCoverage::Full,
        metadata: ['note' => 'laptop encryption control'],
    );

    expect($mapping)->toBeInstanceOf(FrameworkMapping::class)
        ->and($mapping->related_type)->toBe(Control::class)
        ->and($mapping->related_id)->toBe($control->id)
        ->and($mapping->coverage)->toBe(FrameworkMappingCoverage::Full);

    expect($mapping->related)->toBeInstanceOf(Control::class);
    expect($requirement->mappings)->toHaveCount(1);

    Event::assertDispatched('eloquent.created: '.FrameworkMapping::class);
});

test('it cannot map a requirement to an object from another workspace', function (): void {
    $framework = Framework::factory()->withDraftVersion()->create();
    $version = $framework->versions->first();
    $requirement = Trustbird::frameworks()->addRequirement(
        version: $version,
        title: 'Encrypt portable devices',
    );
    $control = Control::factory()->create();

    Trustbird::frameworks()->map(
        requirement: $requirement,
        related: $control,
    );
})->throws(InvalidArgumentException::class, 'Related object must belong to the same workspace.');

test('it cannot modify requirements on a published framework version', function (): void {
    $framework = Framework::factory()->withPublishedVersion()->create();
    $version = $framework->currentVersion;

    Trustbird::frameworks()->addRequirement(
        version: $version,
        title: 'Should not be allowed',
    );
})->throws(InvalidArgumentException::class, 'Only draft framework versions can be modified.');

test('it allows mapping when the related object has no workspace id', function (): void {
    $framework = Framework::factory()->withDraftVersion()->create();
    $version = $framework->versions->first();
    $requirement = Trustbird::frameworks()->addRequirement(
        version: $version,
        title: 'Encrypt portable devices',
    );

    $related = new class
    {
        public string $id = 'related-without-workspace';

        public ?string $workspace_id = null;
    };

    $mapping = Trustbird::frameworks()->map(
        requirement: $requirement,
        related: $related,
    );

    expect($mapping->related_id)->toBe('related-without-workspace')
        ->and($mapping->workspace_id)->toBe($requirement->workspace_id);
});

test('it covers framework model helpers and factories', function (): void {
    $owner = Person::factory()->create();
    $publisher = Person::factory()->create(['workspace_id' => $owner->workspace_id]);

    $framework = Framework::factory()->withPublishedVersion()->create([
        'owner_id' => $owner->id,
        'metadata' => ['source' => 'trustbird'],
    ]);

    $publishedVersion = $framework->currentVersion;
    $publishedVersion->update(['published_by_id' => $publisher->id]);

    $draftVersion = FrameworkVersion::factory()->create([
        'workspace_id' => $framework->workspace_id,
        'framework_id' => $framework->id,
        'version_label' => '2.0-draft',
        'status' => FrameworkVersionStatus::Draft,
    ]);

    $supersededVersion = FrameworkVersion::factory()->create([
        'workspace_id' => $framework->workspace_id,
        'framework_id' => $framework->id,
        'version_label' => '0.9',
        'status' => FrameworkVersionStatus::Superseded,
    ]);

    $requirement = FrameworkRequirement::factory()->create([
        'workspace_id' => $framework->workspace_id,
        'framework_version_id' => $publishedVersion->id,
    ]);

    $control = Control::factory()->create(['workspace_id' => $framework->workspace_id]);

    $mapping = FrameworkMapping::factory()->create([
        'workspace_id' => $framework->workspace_id,
        'requirement_id' => $requirement->id,
        'related_type' => Control::class,
        'related_id' => $control->id,
    ]);

    expect($framework->owner->id)->toBe($owner->id)
        ->and($framework->currentVersion->id)->toBe($publishedVersion->id)
        ->and($framework->versions)->toHaveCount(3);

    expect($publishedVersion->isPublished())->toBeTrue()
        ->and($publishedVersion->isDraft())->toBeFalse()
        ->and($publishedVersion->isSuperseded())->toBeFalse()
        ->and($publishedVersion->canBePublished())->toBeFalse()
        ->and($publishedVersion->publishedBy->id)->toBe($publisher->id)
        ->and($publishedVersion->framework->id)->toBe($framework->id);

    expect($draftVersion->isDraft())->toBeTrue()
        ->and($draftVersion->canBePublished())->toBeTrue();

    expect($supersededVersion->isSuperseded())->toBeTrue();

    expect($requirement->version->id)->toBe($publishedVersion->id)
        ->and($publishedVersion->requirements)->toHaveCount(1)
        ->and($mapping->requirement->id)->toBe($requirement->id);

    expect(Framework::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Framework\FrameworkFactory::class);
    expect(FrameworkVersion::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Framework\FrameworkVersionFactory::class);
    expect(FrameworkRequirement::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Framework\FrameworkRequirementFactory::class);
    expect(FrameworkMapping::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Framework\FrameworkMappingFactory::class);
});
