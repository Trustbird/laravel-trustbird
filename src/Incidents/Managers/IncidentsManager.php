<?php

declare(strict_types=1);

namespace Trustbird\Incidents\Managers;

use DateTimeInterface;
use Trustbird\Incidents\Contracts\HasIncidentNotes;
use Trustbird\Incidents\Contracts\HasIncidents;
use Trustbird\Incidents\Enums\IncidentSeverity;
use Trustbird\Incidents\Enums\IncidentStatus;

final readonly class IncidentsManager
{
    public function create(
        string $title,
        ?string $description = null,
        IncidentSeverity $severity = IncidentSeverity::Medium,
        IncidentStatus $status = IncidentStatus::Open,
        ?string $ownerId = null,
        ?string $responderId = null,
        ?DateTimeInterface $detectedAt = null,
        array $metadata = [],
        ?string $workspaceId = null,
    ): HasIncidents {
        /** @var HasIncidents $model */
        $model = app(HasIncidents::class);

        return $model->query()->create([
            'title' => $title,
            'description' => $description,
            'severity' => $severity,
            'status' => $status,
            'owner_id' => $ownerId,
            'responder_id' => $responderId,
            'detected_at' => $detectedAt,
            'metadata' => $metadata,
            'workspace_id' => $workspaceId,
        ]);
    }

    public function update(
        HasIncidents $incident,
        ?string $title = null,
        ?string $description = null,
        ?IncidentSeverity $severity = null,
        ?IncidentStatus $status = null,
        ?string $ownerId = null,
        ?string $responderId = null,
        ?DateTimeInterface $detectedAt = null,
        ?DateTimeInterface $containedAt = null,
        ?DateTimeInterface $resolvedAt = null,
        ?array $metadata = null,
    ): HasIncidents {
        $attributes = array_filter([
            'title' => $title,
            'description' => $description,
            'severity' => $severity,
            'status' => $status,
            'owner_id' => $ownerId,
            'responder_id' => $responderId,
            'detected_at' => $detectedAt,
            'contained_at' => $containedAt,
            'resolved_at' => $resolvedAt,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $incident->update($attributes);

        return $incident;
    }

    public function addNote(
        HasIncidents $incident,
        string $body,
        ?string $authorId = null,
        ?DateTimeInterface $occurredAt = null,
        array $metadata = [],
    ): HasIncidentNotes {
        /** @var HasIncidentNotes $model */
        $model = app(HasIncidentNotes::class);

        return $model->query()->create([
            'workspace_id' => $incident->workspace_id,
            'incident_id' => $incident->id,
            'author_id' => $authorId,
            'occurred_at' => $occurredAt,
            'body' => $body,
            'metadata' => $metadata,
        ]);
    }
}

