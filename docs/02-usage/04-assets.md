# Assets

The Assets domain manages the company assets and devices that fall within the scope of an organization's trust and compliance.

An Asset can range from physical hardware such as laptops and servers to virtual resources such as cloud accounts and applications. In Trustbird, all these different forms of assets are housed in a unified Asset model.

## Data model

An asset contains the following information:

* **Name**: The name or description of the asset.
* **Kind**: The type of asset (see [Asset Kinds](#asset-kinds)).
* **Owner**: The person (`Person`) responsible for the asset.
* **Provider**: The supplier or manufacturer (e.g., "Apple" or "AWS").
* **Criticality**: The level of criticality of the asset (low, normal, high, critical).
* **Data Classification**: Whether the asset contains personal or sensitive data.
* **Status**: The current status of the asset (e.g., "active").
* **Acquired/Retired date**: When the asset was put into use or retired.
* **Metadata**: Additional structured information (JSON).

## Asset Kinds

The following types are supported via the `AssetKind` enum:

* **Device**: Physical hardware such as laptops, phones, and tablets.
* **System**: Infrastructure components such as servers or network equipment.
* **Application**: Software applications and SaaS solutions.
* **DataStore**: Databases or other data storage locations.
* **Service**: Internal or external services.
* **Account**: User accounts or service accounts at external providers.
* **Location**: Physical locations or data centers.
* **Other**: Other resources that do not fit into the above categories.

## Creating an asset

To create an asset, use the `Trustbird` facade.

```php
use Trustbird\Facades\Trustbird;
use Trustbird\Assets\Enums\AssetKind;

$asset = Trustbird::assets()->create(
    name: 'MacBook Pro 16" - Jane Doe',
    kind: AssetKind::Device,
    ownerId: $person->id,
    providerName: 'Apple',
    criticality: 'high',
    containsPersonalData: true,
);
```

## Updating an asset

Use the `Trustbird` facade to change details of an existing asset.

```php
use Trustbird\Facades\Trustbird;

Trustbird::assets()->update($asset, [
    'name' => 'MacBook Pro 16" - Jane Doe (Replaced)',
    'status' => 'retired',
    'retired_at' => now(),
]);
```

## Deleting an asset

If an asset needs to be completely removed from the system, this can be done via the `Trustbird` facade.

```php
use Trustbird\Facades\Trustbird;

Trustbird::assets()->delete($asset);
```

## Data carriers

The `Asset` model has a useful helper method `isDataCarrier()` to determine if an asset potentially contains data (such as applications, databases, or devices). This is essential for privacy and security reviews.
