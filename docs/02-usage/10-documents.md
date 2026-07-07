## Documents

Documents represent controlled content such as templates, procedures and exportable artefacts. They are independent from policies and support versioning with review metadata.

This module supports:

- creating documents with optional initial draft versions
- drafting and publishing document versions
- recording document-level review dates

### Concepts

- **Document versions** (`DocumentVersionStatus`): draft, published, superseded
- **Current version**: the published version pointer on the document
- **Review metadata**: `reviewed_at`, `next_review_at`, and `reviewer_id` on the document

### Create a document

```php
use Trustbird\Facades\Trustbird;

$document = Trustbird::documents()->create(
    title: 'Business continuity plan',
    content: 'Initial BCP content.',
    ownerId: $person->id,
    reviewerId: $reviewer->id,
);
```

### Publish a version

```php
$version = $document->versions()->where('status', 'draft')->first();

Trustbird::documents()->publishVersion(
    document: $document,
    version: $version,
    publishedById: $person->id,
);
```

### Review a document

```php
Trustbird::documents()->review(
    document: $document,
    reviewerId: $reviewer->id,
    nextReviewAt: now()->addYear(),
);
```
