## Controls

Controls (also called measures) represent practical safeguards you put in place. They are the source model; framework mappings remain a projection on top.

This module supports:

- registering controls with an owner and lifecycle status
- approving controls as part of governance review workflows
- linking controls to risks, policies, evidence and other Trustbird objects

### Concepts

- **Control status** (`ControlStatus`): draft, active, inactive, under review
- **Approve**: marks a control active and records review timestamps
- **Relations**: polymorphic `control_relations` for cross-domain linking

### Create a control

```php
use Trustbird\Facades\Trustbird;
use Trustbird\Controls\Enums\ControlStatus;

$control = Trustbird::controls()->create(
    name: 'Encrypt company laptops',
    description: 'All laptops must use full-disk encryption.',
    status: ControlStatus::Draft,
    ownerId: $person->id,
);
```

### Approve a control

```php
Trustbird::controls()->approve(
    control: $control,
    nextReviewAt: now()->addYear(),
);
```

### Relate a control

```php
Trustbird::controls()->relate(
    control: $control,
    related: $risk,
    metadata: ['mapping' => 'risk mitigation'],
);
```
