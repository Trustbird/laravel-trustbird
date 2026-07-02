# Risks

The Risks domain helps organizations register and manage business risks in plain language, without forcing users into audit or compliance jargon.

A risk describes something that could go wrong and the impact it would have on the organization. Risks are owned by a person, follow an explicit lifecycle, and can be reviewed on a schedule.

## Design principles

- Use everyday language for titles, descriptions, and statuses.
- Keep assessment simple with likelihood and impact levels.
- Make ownership explicit so someone is accountable.
- Reserve `metadata` for future links to measures, controls, assets, suppliers, and frameworks.

## Data model

A risk contains the following information:

* **Title**: A short, plain-language description of what could go wrong.
* **Description**: Additional context about the risk and why it matters.
* **Owner**: The person (`Person`) responsible for monitoring and addressing the risk.
* **Status**: Where the risk sits in its lifecycle (see [Risk Statuses](#risk-statuses)).
* **Treatment**: How the organization plans to handle the risk (see [Risk Treatments](#risk-treatments)).
* **Likelihood**: How likely the risk is to occur (low, medium, high).
* **Impact**: How severe the consequences would be (low, medium, high).
* **Reviewed at**: When the risk was last reviewed.
* **Next review at**: When the risk should be reviewed again.
* **Metadata**: Additional structured information (JSON).

## Risk Statuses

The following lifecycle statuses are supported via the `RiskStatus` enum:

* **Open**: The risk has been registered and is awaiting assessment.
* **Under review**: The risk is being assessed.
* **Being addressed**: Treatment is in progress.
* **Accepted**: The organization has consciously accepted the risk.
* **Resolved**: The risk has been addressed and is no longer active.
* **Archived**: The risk is kept for reference but is no longer active.

## Risk Treatments

The following treatment options are supported via the `RiskTreatment` enum:

* **Accept**: Live with the risk as it is.
* **Reduce**: Take action to lower likelihood or impact.
* **Avoid**: Stop the activity that causes the risk.
* **Transfer**: Shift the risk elsewhere, such as through insurance or outsourcing.
* **Monitor**: Watch the risk without immediate action.

## Risk Levels

Likelihood and impact use the `RiskLevel` enum:

* **Low**
* **Medium**
* **High**

## Creating a risk

To register a risk, use the `CreateRisk` action.

```php
use Trustbird\Risks\Actions\CreateRisk;
use Trustbird\Risks\Enums\RiskLevel;

$risk = app(CreateRisk::class)->handle([
    'title' => 'Laptop theft during travel',
    'description' => 'Employees travel with company laptops that may be lost or stolen.',
    'owner_id' => $person->id,
    'likelihood' => RiskLevel::Medium,
    'impact' => RiskLevel::High,
]);
```

## Updating a risk

Use the `UpdateRisk` action to change details of an existing risk.

```php
use Trustbird\Risks\Actions\UpdateRisk;
use Trustbird\Risks\Enums\RiskStatus;
use Trustbird\Risks\Enums\RiskTreatment;

app(UpdateRisk::class)->handle($risk, [
    'status' => RiskStatus::BeingAddressed,
    'treatment' => RiskTreatment::Reduce,
]);
```

## Reviewing a risk

Use the `ReviewRisk` action to record a review. This sets `reviewed_at` automatically and can update status, treatment, likelihood, impact, and the next review date.

```php
use Trustbird\Risks\Actions\ReviewRisk;
use Trustbird\Risks\Enums\RiskStatus;

app(ReviewRisk::class)->handle($risk, [
    'status' => RiskStatus::Accepted,
    'next_review_at' => now()->addMonths(3),
]);
```

## Lifecycle helpers

The `Risk` model provides helper methods for common lifecycle checks:

* `isActive()`: The risk is neither resolved nor archived.
* `isResolved()`: The risk has been resolved.
* `isArchived()`: The risk has been archived.
* `needsReview()`: The risk is active and either has no next review date or is past due.

## Future relationships

The risks domain is intentionally standalone for now. Future modules can link risks to measures, controls, assets, suppliers, and frameworks through dedicated relationships or `metadata` without changing the core registration model.
