## Suppliers

Suppliers represent third parties (vendors, service providers, partners) that can influence trust, risk and evidence.

This module supports:

- registering suppliers with an owner, status and criticality
- recording review metadata (`reviewed_at`, `next_review_at`)
- creating relations to other Trustbird objects (future linking foundation)

### Concepts

- **Supplier status** (`SupplierStatus`): operational lifecycle state (active, inactive, offboarded)
- **Supplier criticality** (`SupplierCriticality`): how critical the supplier is to your operations and trust posture
- **Review metadata**: timestamps used for review workflows; future questionnaires and evidence collection can build on this
- **Relations**: a polymorphic relation table (`supplier_relations`) for linking suppliers to other Trustbird objects

### Create a supplier

```php
use Trustbird\Facades\Trustbird;
use Trustbird\Suppliers\Enums\SupplierCriticality;
use Trustbird\Suppliers\Enums\SupplierStatus;

$supplier = Trustbird::suppliers()->create(
    name: 'Acme Hosting',
    description: 'Primary hosting provider.',
    status: SupplierStatus::Active,
    criticality: SupplierCriticality::High,
    ownerId: $person->id,
);
```

### Review a supplier

```php
use Trustbird\Facades\Trustbird;

Trustbird::suppliers()->review(
    supplier: $supplier,
    reviewedAt: now(),
    nextReviewAt: now()->addMonths(6),
);
```

### Relate a supplier to other Trustbird objects

Relations are intentionally generic so future modules (assets, evidence, framework mappings) can attach without reshaping the supplier tables.

```php
use Trustbird\Facades\Trustbird;

Trustbird::suppliers()->relate(
    supplier: $supplier,
    related: $risk,
    metadata: ['reason' => 'risk review identified vendor dependency'],
);
```

