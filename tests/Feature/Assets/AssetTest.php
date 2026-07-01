<?php

declare(strict_types=1);

use Trustbird\Assets\Actions\CreateAsset;
use Trustbird\Assets\Actions\DeleteAsset;
use Trustbird\Assets\Actions\UpdateAsset;
use Trustbird\Assets\Enums\AssetKind;
use Trustbird\Assets\Events\AssetCreated;
use Trustbird\Assets\Events\AssetDeleted;
use Trustbird\Assets\Events\AssetUpdated;
use Trustbird\Assets\Models\Asset;
use Trustbird\People\Models\Person;
use Illuminate\Support\Facades\Event;

test('it can create an asset and dispatches event', function () {
    Event::fake();
    
    $person = Person::factory()->create();
    
    $action = new CreateAsset();
    $asset = $action->handle([
        'name' => 'MacBook Pro',
        'kind' => AssetKind::Device,
        'owner_id' => $person->id,
        'provider_name' => 'Apple',
    ]);

    expect($asset)->toBeInstanceOf(Asset::class)
        ->name->toBe('MacBook Pro')
        ->kind->toBe(AssetKind::Device)
        ->owner_id->toBe($person->id);
    
    expect($asset->owner)->toBeInstanceOf(Person::class);
    
    Event::assertDispatched(AssetCreated::class, function ($event) use ($asset) {
        return $event->asset->id === $asset->id;
    });
});

test('it can update an asset and dispatches event', function () {
    Event::fake();
    
    $asset = Asset::factory()->create(['name' => 'Old Name']);
    
    $action = new UpdateAsset();
    $updatedAsset = $action->handle($asset, ['name' => 'New Name']);

    expect($updatedAsset->name)->toBe('New Name');
    
    Event::assertDispatched(AssetUpdated::class, function ($event) use ($asset) {
        return $event->asset->id === $asset->id;
    });
});

test('it can delete an asset and dispatches event', function () {
    Event::fake();
    
    $asset = Asset::factory()->create();
    
    $action = new DeleteAsset();
    $result = $action->handle($asset);

    expect($result)->toBeTrue();
    expect(Asset::query()->count())->toBe(0);
    
    Event::assertDispatched(AssetDeleted::class, function ($event) use ($asset) {
        return $event->asset->id === $asset->id;
    });
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
        
    expect($device->acquired_at)->toBeInstanceOf(\Carbon\CarbonInterface::class);
    expect($device->contains_personal_data)->toBeBool();
    expect($device->contains_sensitive_data)->toBeBool();
    
    // Test factory
    expect(Asset::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Asset\AssetFactory::class);
});
