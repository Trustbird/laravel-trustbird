<?php

declare(strict_types=1);

namespace Trustbird\Risks\Managers;

use DateTimeInterface;
use Trustbird\Risks\Actions\ReviewRisk;
use Trustbird\Risks\Contracts\HasRisks;
use Trustbird\Risks\Enums\RiskLevel;
use Trustbird\Risks\Enums\RiskStatus;
use Trustbird\Risks\Enums\RiskTreatment;

final readonly class RisksManager
{
    public function create(
        string $title,
        ?string $description = null,
        ?string $ownerId = null,
        RiskStatus $status = RiskStatus::Open,
        ?RiskTreatment $treatment = null,
        ?RiskLevel $likelihood = null,
        ?RiskLevel $impact = null,
        array $metadata = [],
        ?string $workspaceId = null,
    ): HasRisks {
        /** @var HasRisks $model */
        $model = app(HasRisks::class);

        return $model->query()->create([
            'title' => $title,
            'description' => $description,
            'owner_id' => $ownerId,
            'status' => $status,
            'treatment' => $treatment,
            'likelihood' => $likelihood,
            'impact' => $impact,
            'metadata' => $metadata,
            'workspace_id' => $workspaceId,
        ]);
    }

    public function update(
        HasRisks $risk,
        ?string $title = null,
        ?string $description = null,
        ?string $ownerId = null,
        ?RiskStatus $status = null,
        ?RiskTreatment $treatment = null,
        ?RiskLevel $likelihood = null,
        ?RiskLevel $impact = null,
        ?array $metadata = null,
    ): HasRisks {
        $attributes = array_filter([
            'title' => $title,
            'description' => $description,
            'owner_id' => $ownerId,
            'status' => $status,
            'treatment' => $treatment,
            'likelihood' => $likelihood,
            'impact' => $impact,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $risk->update($attributes);

        return $risk;
    }

    public function accept(HasRisks $risk, ?string $notes = null): HasRisks
    {
        return app(ReviewRisk::class)->handle($risk, [
            'status' => RiskStatus::Accepted,
            'notes' => $notes,
            'reviewed_at' => now(),
        ]);
    }

    public function review(
        HasRisks $risk,
        ?RiskStatus $status = null,
        ?RiskTreatment $treatment = null,
        ?RiskLevel $likelihood = null,
        ?RiskLevel $impact = null,
        ?DateTimeInterface $reviewedAt = null,
        ?DateTimeInterface $nextReviewAt = null,
        ?string $notes = null,
    ): HasRisks {
        $attributes = array_filter([
            'status' => $status,
            'treatment' => $treatment,
            'likelihood' => $likelihood,
            'impact' => $impact,
            'reviewed_at' => $reviewedAt ?? now(),
            'next_review_at' => $nextReviewAt,
            'notes' => $notes,
        ], fn ($value) => $value !== null);

        return app(ReviewRisk::class)->handle($risk, $attributes);
    }
}
