<?php

declare(strict_types=1);

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Event;
use Trustbird\Assets\Enums\AssetKind;
use Trustbird\Assets\Models\Asset;
use Trustbird\Database\Factories\Asset\AssetFactory;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Models\Person;

test('it can create an asset and dispatches event', function () {
    Event::fake();

    $person = Person::factory()->create();

    $asset = Trustbird::assets()->create(
        name: 'MacBook Pro',
        kind: AssetKind::Device,
        ownerId: $person->id,
        providerName: 'Apple',
    );

    expect($asset)->toBeInstanceOf(Asset::class)
        ->name->toBe('MacBook Pro')
        ->kind->toBe(AssetKind::Device)
        ->owner_id->toBe($person->id);

    expect($asset->owner)->toBeInstanceOf(Person::class);

    Event::assertDispatched('eloquent.created: '.Asset::class);
});

test('it can update an asset and dispatches event', function () {
    Event::fake();

    $asset = Asset::factory()->create(['name' => 'Old Name']);

    $updatedAsset = Trustbird::assets()->update($asset, name: 'New Name');

    expect($updatedAsset->name)->toBe('New Name');

    Event::assertDispatched('eloquent.updated: '.Asset::class);
});

test('it can delete an asset and dispatches event', function () {
    Event::fake();

    $asset = Asset::factory()->create();

    $result = Trustbird::assets()->delete($asset);

    expect($result)->toBeTrue();
    expect(Asset::query()->count())->toBe(0);

    Event::assertDispatched('eloquent.deleted: '.Asset::class);
});

test('it can identify a data carrier', function () {
    $device = Asset::factory()->device()->create();
    $location = Asset::factory()->state(['kind' => AssetKind::Location])->create();

    expect($device->isDataCarrier())->toBeTrue();
    expect($location->isDataCarrier())->toBeFalse();
});

test('it covers all asset model methods', function () {
    $person = Person::factory()->create();
    $device = Asset::factory()->state([
        'kind' => AssetKind::Device,
        'owner_id' => $person->id,
        'metadata' => ['os' => 'macOS'],
        'acquired_at' => now(),
    ])->create();

    $system = Asset::factory()->state(['kind' => AssetKind::System])->create();
    $application = Asset::factory()->state(['kind' => AssetKind::Application])->create();
    $dataStore = Asset::factory()->state(['kind' => AssetKind::DataStore])->create();
    $service = Asset::factory()->state(['kind' => AssetKind::Service])->create();
    $account = Asset::factory()->state(['kind' => AssetKind::Account])->create();
    $other = Asset::factory()->state(['kind' => AssetKind::Other])->create();

    // Test model methods
    expect($device->isDevice())->toBeTrue()
        ->and($device->isSystem())->toBeFalse()
        ->and($device->isDataCarrier())->toBeTrue();

    expect($system->isDevice())->toBeFalse()
        ->and($system->isSystem())->toBeTrue()
        ->and($system->isDataCarrier())->toBeTrue();

    expect($application->isDataCarrier())->toBeTrue();
    expect($dataStore->isDataCarrier())->toBeTrue();
    expect($service->isDataCarrier())->toBeTrue();
    expect($account->isDataCarrier())->toBeTrue();
    expect($other->isDataCarrier())->toBeFalse();

    // Test relations and casts
    expect($device->owner)->toBeInstanceOf(Person::class)
        ->and($device->owner->id)->toBe($person->id);

    expect($device->metadata)->toBeArray()
        ->and($device->metadata['os'])->toBe('macOS');

    expect($device->acquired_at)->toBeInstanceOf(CarbonInterface::class);
    expect($device->contains_personal_data)->toBeBool();
    expect($device->contains_sensitive_data)->toBeBool();

    // Test factory
    expect(Asset::newFactory())->toBeInstanceOf(AssetFactory::class);
});
