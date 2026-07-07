# Incidents

The Incidents domain provides the first scaffolding for tracking security, privacy, and operational events in Trustbird.

Incidents are plain-language records that can be registered, assigned, updated, and enriched with a simple timeline of notes. Future workflows such as tabletop exercises, evidence collection, and formal reviews can build on this foundation.

## Design principles

- Keep incident registration simple and business-friendly.
- Make severity and lifecycle explicit through enums.
- Treat timeline notes as first-class records.
- Reserve `metadata` for future integrations (evidence, tabletop, review workflows).

## Data model

### Incident

An incident contains:

* **Title**: Short description of what happened (or might have happened).
* **Description**: Additional context.
* **Severity**: How serious the incident is (`IncidentSeverity`).
* **Status**: Where the incident is in its lifecycle (`IncidentStatus`).
* **Owner**: The person accountable for the incident.
* **Responder**: The person actively responding.
* **Detected/Contained/Resolved timestamps**: Optional lifecycle timestamps.
* **Metadata**: Additional structured information (JSON).

### Incident note

An incident note represents a timeline entry:

* **Body**: The note text.
* **Author**: Optional author (`Person`).
* **Occurred at**: Optional timestamp for when the note event occurred.
* **Metadata**: Additional structured information (JSON).

## Severity

The following severities are supported via the `IncidentSeverity` enum:

* **Low**
* **Medium**
* **High**
* **Critical**

## Status

The following statuses are supported via the `IncidentStatus` enum:

* **Open**
* **Investigating**
* **Contained**
* **Resolved**
* **Archived**

## Registering an incident

Use the facade for stable public API access.

```php
use Trustbird\Facades\Trustbird;
use Trustbird\Incidents\Enums\IncidentSeverity;

$incident = Trustbird::incidents()->create(
    title: 'Suspicious login detected',
    description: 'Multiple failed attempts followed by a successful login.',
    severity: IncidentSeverity::High,
    ownerId: $owner->id,
    responderId: $responder->id,
);
```

## Updating an incident

```php
use Trustbird\Facades\Trustbird;
use Trustbird\Incidents\Enums\IncidentStatus;

Trustbird::incidents()->update(
    incident: $incident,
    status: IncidentStatus::Investigating,
    containedAt: now(),
);
```

## Adding timeline notes

```php
use Trustbird\Facades\Trustbird;

Trustbird::incidents()->addNote(
    incident: $incident,
    body: 'Initial triage completed.',
    authorId: $responder->id,
    occurredAt: now(),
);
```

