## Frameworks

Frameworks are a **projection** over Trustbird's canonical model. Controls, policies, risks and evidence remain the source of truth; framework requirements and mappings describe how those objects relate to an external readiness view.

This module intentionally uses Trustbird-owned language. It does not store or reproduce third-party standard text.

### Concepts

- **Framework**: a named readiness catalogue owned by the workspace
- **Framework version** (`FrameworkVersionStatus`): draft, published, superseded
- **Requirement**: a Trustbird-worded expectation inside a version (`code`, `title`, `summary`)
- **Mapping**: links a requirement to a canonical Trustbird object (`control`, `policy`, `risk`, `evidence`, …) with coverage (`FrameworkMappingCoverage`)

### Create a framework

```php
use Trustbird\Facades\Trustbird;

$framework = Trustbird::frameworks()->create(
    name: 'Information security readiness',
    description: 'A plain-language checklist for small organisations.',
    versionLabel: '1.0',
    ownerId: $person->id,
);
```

### Add a requirement

```php
$version = $framework->versions->first();

$requirement = Trustbird::frameworks()->addRequirement(
    version: $version,
    title: 'Know who can access sensitive systems',
    code: 'ACCESS-1',
    summary: 'Keep a clear list of people with privileged access.',
);
```

### Publish a version

```php
Trustbird::frameworks()->publishVersion(
    framework: $framework,
    version: $version,
    publishedById: $person->id,
);
```

### Map a requirement to a canonical object

```php
use Trustbird\Frameworks\Enums\FrameworkMappingCoverage;

Trustbird::frameworks()->map(
    requirement: $requirement,
    related: $control,
    coverage: FrameworkMappingCoverage::Full,
);
```
