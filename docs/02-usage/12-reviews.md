## Reviews

Reviews provide a reusable governance workflow across policies, risks, evidence, suppliers, controls and other Trustbird objects.

Each review is stored as its own record so history is preserved when items are completed or reopened.

### Concepts

- **Review status** (`ReviewStatus`): scheduled, completed, reopened
- **Due dates**: `due_at` on scheduled reviews
- **Reviewer relationships**: primary reviewer on the review plus optional `review_reviewers` assignments
- **Polymorphic subject**: `reviewable_type` / `reviewable_id`

### Schedule a review

```php
use Trustbird\Facades\Trustbird;

$review = Trustbird::reviews()->schedule(
    subject: $supplier,
    dueAt: now()->addMonths(6),
    reviewerId: $reviewer->id,
);
```

### Complete a review

```php
Trustbird::reviews()->complete(
    review: $review,
    reviewerId: $reviewer->id,
    notes: 'Supplier controls verified.',
);
```

### Reopen a completed review

```php
Trustbird::reviews()->reopen(review: $review);
```

### Assign additional reviewers

```php
use Trustbird\Reviews\Enums\ReviewerRole;

Trustbird::reviews()->assignReviewer(
    review: $review,
    personId: $contributor->id,
    role: ReviewerRole::Contributor,
);
```
