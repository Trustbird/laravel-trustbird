# Policies

The Policies domain manages versioned, reviewable organizational policies in plain language.

A policy is a stable container for ownership, review scheduling, and publication state. The actual policy text lives in versioned records so changes can be drafted, reviewed, and published explicitly.

## Design principles

- Separate policy metadata from versioned content.
- Keep publication explicit through a dedicated action.
- Assign clear ownership and review responsibility.
- Reserve `metadata` for future links to controls, frameworks, and evidence.

## Data model

### Policy

A policy contains:

* **Title**: The name of the policy (e.g. "Information Security Policy").
* **Owner**: The person (`Person`) responsible for maintaining the policy.
* **Reviewer**: The person (`Person`) responsible for reviewing the policy.
* **Published version**: A pointer to the currently published `PolicyVersion`.
* **Reviewed at**: When the policy was last reviewed.
* **Next review at**: When the policy should be reviewed again.
* **Metadata**: Additional structured information (JSON).

### Policy version

Each version contains:

* **Version number**: A sequential number within the policy.
* **Status**: The version lifecycle state (see [Version statuses](#version-statuses)).
* **Content**: The policy text.
* **Change summary**: A short description of what changed in this version.
* **Published at**: When the version was published.
* **Published by**: The person who published the version.
* **Metadata**: Additional structured information (JSON).

## Version statuses

The following statuses are supported via the `PolicyVersionStatus` enum:

* **Draft**: Work in progress, editable, not yet active.
* **Published**: The currently active version for the organization.
* **Superseded**: A previously published version replaced by a newer one.

## Creating a policy

`CreatePolicy` registers a policy and its first draft version.

```php
use Trustbird\Policies\Actions\CreatePolicy;

$policy = app(CreatePolicy::class)->handle([
    'title' => 'Information Security Policy',
    'content' => 'All employees must protect company data.',
    'owner_id' => $owner->id,
    'reviewer_id' => $reviewer->id,
]);
```

## Updating a policy

Use `UpdatePolicy` to change policy metadata such as title, owner, or reviewer.

```php
use Trustbird\Policies\Actions\UpdatePolicy;

app(UpdatePolicy::class)->handle($policy, [
    'title' => 'Information Security and Privacy Policy',
    'reviewer_id' => $newReviewer->id,
]);
```

## Drafting a new version

Use `DraftPolicyVersion` to start a new draft based on the next version number.

```php
use Trustbird\Policies\Actions\DraftPolicyVersion;

$version = app(DraftPolicyVersion::class)->handle($policy, [
    'content' => 'Updated policy text.',
    'change_summary' => 'Annual review updates',
]);
```

## Updating a draft version

Use `UpdatePolicyVersion` to edit draft content. Published and superseded versions cannot be changed.

```php
use Trustbird\Policies\Actions\UpdatePolicyVersion;

app(UpdatePolicyVersion::class)->handle($version, [
    'content' => 'Revised draft content.',
]);
```

## Publishing a version

Publication is always explicit. `PublishPolicyVersion` activates a draft version and supersedes the previously published version, if any.

```php
use Trustbird\Policies\Actions\PublishPolicyVersion;

app(PublishPolicyVersion::class)->handle($policy, $version, [
    'published_by_id' => $publisher->id,
]);
```

## Reviewing a policy

Use `ReviewPolicy` to record a review and schedule the next one.

```php
use Trustbird\Policies\Actions\ReviewPolicy;

app(ReviewPolicy::class)->handle($policy, [
    'reviewer_id' => $reviewer->id,
    'next_review_at' => now()->addYear(),
]);
```

## Lifecycle helpers

The `Policy` model provides:

* `hasPublishedVersion()`: Whether a published version exists.
* `needsReview()`: Whether the policy has no next review date or is past due.

The `PolicyVersion` model provides:

* `isDraft()`, `isPublished()`, `isSuperseded()`
* `canBeEdited()`: Whether the version can be updated.
* `canBePublished()`: Whether the version can be published.

## Future relationships

The policies domain is intentionally standalone for now. Future modules can link policies to controls, frameworks, evidence, and training requirements without changing the core versioning model.
