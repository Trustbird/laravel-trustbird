<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Assets\Enums\AssetKind;
use Trustbird\Assets\Models\Asset;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Models\Person;

beforeEach(fn () => Event::fake());

it('can create an asset via the facade', function () {
    $asset = Trustbird::assets()->create(
        name: 'MacBook Pro',
        kind: AssetKind::Device
    );

    expect($asset)->toBeInstanceOf(Asset::class)
        ->and($asset->name)->toBe('MacBook Pro')
        ->and($asset->kind)->toBe(AssetKind::Device);

    Event::assertDispatched('eloquent.created: '.Asset::class);
});

it('can update an asset via the facade', function () {
    $asset = Asset::factory()->create(['name' => 'Old Name']);
    $owner = Person::factory()->create();
    $acquiredAt = now()->subMonth();
    $retiredAt = now();

    $updated = Trustbird::assets()->update(
        $asset,
        name: 'New Name',
        kind: AssetKind::System,
        description: 'New Description',
        ownerId: $owner->id,
        providerName: 'AWS',
        externalReference: 'REF-123',
        environment: 'production',
        criticality: 'critical',
        containsPersonalData: true,
        containsSensitiveData: true,
        status: 'archived',
        acquiredAt: $acquiredAt,
        retiredAt: $retiredAt,
        metadata: ['tags' => ['pii']]
    );

    expect($updated->name)->toBe('New Name')
        ->and($updated->kind)->toBe(AssetKind::System)
        ->and($updated->description)->toBe('New Description')
        ->and($updated->owner_id)->toBe($owner->id)
        ->and($updated->provider_name)->toBe('AWS')
        ->and($updated->external_reference)->toBe('REF-123')
        ->and($updated->environment)->toBe('production')
        ->and($updated->criticality)->toBe('critical')
        ->and($updated->contains_personal_data)->toBeTrue()
        ->and($updated->contains_sensitive_data)->toBeTrue()
        ->and($updated->status)->toBe('archived')
        ->and($updated->acquired_at->format('Y-m-d'))->toBe($acquiredAt->format('Y-m-d'))
        ->and($updated->retired_at->format('Y-m-d'))->toBe($retiredAt->format('Y-m-d'))
        ->and($updated->metadata)->toBe(['tags' => ['pii']]);

    Event::assertDispatched('eloquent.updated: '.Asset::class);
});

it('can retire an asset via the facade', function () {
    $asset = Asset::factory()->create();

    $retired = Trustbird::assets()->retire($asset);

    expect($retired->retired_at)->not->toBeNull();
    Event::assertDispatched('eloquent.updated: '.Asset::class);
});

it('can delete an asset via the facade', function () {
    $asset = Asset::factory()->create();

    $result = Trustbird::assets()->delete($asset);

    expect($result)->toBeTrue();
    Event::assertDispatched('eloquent.deleted: '.Asset::class);
});
