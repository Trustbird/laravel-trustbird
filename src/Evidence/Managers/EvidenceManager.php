<?php

declare(strict_types=1);

namespace Trustbird\Evidence\Managers;

use DateTimeInterface;
use Trustbird\Evidence\Actions\ReviewEvidence;
use Trustbird\Evidence\Contracts\HasEvidence;
use Trustbird\Evidence\Contracts\HasEvidenceRelations;
use Trustbird\Evidence\Enums\EvidenceStatus;
use Trustbird\Evidence\Enums\EvidenceType;
use Trustbird\Evidence\Models\Evidence;

final readonly class EvidenceManager
{
    public function create(
        string $title,
        ?string $description = null,
        EvidenceType $type = EvidenceType::Other,
        EvidenceStatus $status = EvidenceStatus::Draft,
        ?string $ownerId = null,
        ?string $reviewerId = null,
        ?DateTimeInterface $reviewedAt = null,
        ?DateTimeInterface $nextReviewAt = null,
        ?string $externalUrl = null,
        ?string $storageKey = null,
        array $metadata = [],
        ?string $workspaceId = null,
    ): HasEvidence {
        /** @var HasEvidence $model */
        $model = app(HasEvidence::class);

        return $model->query()->create([
            'title' => $title,
            'description' => $description,
            'type' => $type,
            'status' => $status,
            'owner_id' => $ownerId,
            'reviewer_id' => $reviewerId,
            'reviewed_at' => $reviewedAt,
            'next_review_at' => $nextReviewAt,
            'external_url' => $externalUrl,
            'storage_key' => $storageKey,
            'metadata' => $metadata,
            'workspace_id' => $workspaceId,
        ]);
    }

    public function update(
        HasEvidence $evidence,
        ?string $title = null,
        ?string $description = null,
        ?EvidenceType $type = null,
        ?EvidenceStatus $status = null,
        ?string $ownerId = null,
        ?string $reviewerId = null,
        ?DateTimeInterface $reviewedAt = null,
        ?DateTimeInterface $nextReviewAt = null,
        ?string $externalUrl = null,
        ?string $storageKey = null,
        ?array $metadata = null,
    ): HasEvidence {
        $attributes = array_filter([
            'title' => $title,
            'description' => $description,
            'type' => $type,
            'status' => $status,
            'owner_id' => $ownerId,
            'reviewer_id' => $reviewerId,
            'reviewed_at' => $reviewedAt,
            'next_review_at' => $nextReviewAt,
            'external_url' => $externalUrl,
            'storage_key' => $storageKey,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $evidence->update($attributes);

        return $evidence;
    }

    public function review(
        HasEvidence $evidence,
        ?DateTimeInterface $reviewedAt = null,
        ?DateTimeInterface $nextReviewAt = null,
        ?string $reviewerId = null,
        ?EvidenceStatus $status = null,
    ): HasEvidence {
        return app(ReviewEvidence::class)->handle($evidence, array_filter([
            'reviewed_at' => $reviewedAt,
            'next_review_at' => $nextReviewAt,
            'reviewer_id' => $reviewerId,
            'status' => $status,
        ], fn ($value) => $value !== null));
    }

    public function relate(
        Evidence $evidence,
        object $related,
        array $metadata = [],
    ): HasEvidenceRelations {
        /** @var HasEvidenceRelations $model */
        $model = app(HasEvidenceRelations::class);

        return $model->query()->create([
            'workspace_id' => $evidence->workspace_id,
            'evidence_id' => $evidence->id,
            'related_type' => $related::class,
            'related_id' => $related->id,
            'metadata' => $metadata,
        ]);
    }
}
