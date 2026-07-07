## Evidence

Evidence items represent proof that supports policies, risks, controls, reviews and future framework readiness checks.

This module supports:

- registering evidence as documents, links, uploads or notes
- review metadata and sensitivity hints via `metadata`
- polymorphic relations to other Trustbird objects

### Concepts

- **Evidence type** (`EvidenceType`): document, link, upload, note, other
- **Evidence status** (`EvidenceStatus`): draft, active, under review, archived
- **Sensitive metadata**: use `metadata` (for example `sensitivity`) and `storage_key` instead of exposing raw storage paths in the public API
- **Relations**: polymorphic `evidence_relations` for linking proof to controls, risks and more

### Register evidence

```php
use Trustbird\Facades\Trustbird;
use Trustbird\Evidence\Enums\EvidenceType;

$evidence = Trustbird::evidence()->create(
    title: 'Penetration test report',
    type: EvidenceType::Link,
    externalUrl: 'https://example.com/report.pdf',
    metadata: ['sensitivity' => 'confidential'],
);
```

### Review evidence

```php
use Trustbird\Evidence\Enums\EvidenceStatus;

Trustbird::evidence()->review(
    evidence: $evidence,
    reviewerId: $reviewer->id,
    status: EvidenceStatus::Active,
    nextReviewAt: now()->addMonths(6),
);
```

### Relate evidence

```php
Trustbird::evidence()->relate(
    evidence: $evidence,
    related: $control,
    metadata: ['purpose' => 'audit proof'],
);
```
