# Custom Models

Trustbird allows you to replace its default models with your own. This is useful if you want to add your own relationships, logic, or properties to Trustbird's core entities.

## Implementation

To use your own model, it must:

1.  Implement the corresponding domain Contract (Interface).
2.  Use the corresponding domain Concern (Trait).

### Example: Custom Person Model

If you want to use your own `User` model as a Trustbird `Person`:

```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\People\Models\Concerns\InteractsWithPeople;

class User extends Authenticatable implements HasPeople
{
    use InteractsWithPeople;
}
```

## Configuration

After creating your custom model, you must register it in the `config/trustbird.php` configuration file:

```php
return [
    'models' => [
        'person' => App\Models\User::class,
    ],
];
```

## Naming Conventions

Trustbird follows a strict naming convention for its extension points, inspired by Filament:

### Contracts (Interfaces)

Interfaces for domain models always use the plural `Has{Domain}s` convention.

*   `HasPeople`
*   `HasAssets`
*   `HasTeams`
*   `HasRisks`
*   `HasWorkspaces`

### Concerns (Traits)

Traits that provide domain functionality always use the plural `InteractsWith{Domain}s` convention.

*   `InteractsWithPeople`
*   `InteractsWithAssets`
*   `InteractsWithTeams`
*   `InteractsWithRisks`
*   `InteractsWithWorkspaces`
