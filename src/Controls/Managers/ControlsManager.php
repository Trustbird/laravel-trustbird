<?php

declare(strict_types=1);

namespace Trustbird\Controls\Managers;

use DateTimeInterface;
use Trustbird\Controls\Actions\ApproveControl;
use Trustbird\Controls\Contracts\HasControlRelations;
use Trustbird\Controls\Contracts\HasControls;
use Trustbird\Controls\Enums\ControlStatus;
use Trustbird\Controls\Models\Control;

final readonly class ControlsManager
{
    public function create(
        string $name,
        ?string $description = null,
        ControlStatus $status = ControlStatus::Draft,
        ?string $ownerId = null,
        ?DateTimeInterface $reviewedAt = null,
        ?DateTimeInterface $nextReviewAt = null,
        array $metadata = [],
        ?string $workspaceId = null,
    ): HasControls {
        /** @var HasControls $model */
        $model = app(HasControls::class);

        return $model->query()->create([
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'owner_id' => $ownerId,
            'reviewed_at' => $reviewedAt,
            'next_review_at' => $nextReviewAt,
            'metadata' => $metadata,
            'workspace_id' => $workspaceId,
        ]);
    }

    public function update(
        HasControls $control,
        ?string $name = null,
        ?string $description = null,
        ?ControlStatus $status = null,
        ?string $ownerId = null,
        ?DateTimeInterface $reviewedAt = null,
        ?DateTimeInterface $nextReviewAt = null,
        ?array $metadata = null,
    ): HasControls {
        $attributes = array_filter([
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'owner_id' => $ownerId,
            'reviewed_at' => $reviewedAt,
            'next_review_at' => $nextReviewAt,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $control->update($attributes);

        return $control;
    }

    public function approve(
        HasControls $control,
        ?DateTimeInterface $reviewedAt = null,
        ?DateTimeInterface $nextReviewAt = null,
    ): HasControls {
        return app(ApproveControl::class)->handle($control, array_filter([
            'reviewed_at' => $reviewedAt,
            'next_review_at' => $nextReviewAt,
        ], fn ($value) => $value !== null));
    }

    public function relate(
        Control $control,
        object $related,
        array $metadata = [],
    ): HasControlRelations {
        /** @var HasControlRelations $model */
        $model = app(HasControlRelations::class);

        return $model->query()->create([
            'workspace_id' => $control->workspace_id,
            'control_id' => $control->id,
            'related_type' => $related::class,
            'related_id' => $related->id,
            'metadata' => $metadata,
        ]);
    }
}
