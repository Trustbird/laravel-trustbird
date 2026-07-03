<?php

declare(strict_types=1);

namespace Trustbird\Risks\Actions;

use Trustbird\Risks\Contracts\HasRisks;
use Trustbird\Risks\Enums\RiskLevel;
use Trustbird\Risks\Enums\RiskStatus;
use Trustbird\Risks\Enums\RiskTreatment;
use Trustbird\Risks\Events\RiskReviewed;

final readonly class ReviewRisk
{
    /**
     * @param array{
     *     reviewed_at?: \DateTimeInterface|null,
     *     next_review_at?: \DateTimeInterface|null,
     *     status?: string|RiskStatus,
     *     treatment?: string|RiskTreatment|null,
     *     likelihood?: string|RiskLevel|null,
     *     impact?: string|RiskLevel|null,
     * } $attributes
     */
    public function handle(HasRisks $risk, array $attributes = []): HasRisks
    {
        $risk->update([
            ...$attributes,
            'reviewed_at' => $attributes['reviewed_at'] ?? now(),
        ]);

        RiskReviewed::dispatch($risk);

        return $risk;
    }
}
