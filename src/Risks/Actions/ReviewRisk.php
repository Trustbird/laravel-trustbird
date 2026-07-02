<?php

declare(strict_types=1);

namespace Trustbird\Risks\Actions;

use Trustbird\Risks\Events\RiskReviewed;
use Trustbird\Risks\Models\Risk;

final readonly class ReviewRisk
{
    /**
     * @param array{
     *     reviewed_at?: \DateTimeInterface|null,
     *     next_review_at?: \DateTimeInterface|null,
     *     status?: string|\Trustbird\Risks\Enums\RiskStatus,
     *     treatment?: string|\Trustbird\Risks\Enums\RiskTreatment|null,
     *     likelihood?: string|\Trustbird\Risks\Enums\RiskLevel|null,
     *     impact?: string|\Trustbird\Risks\Enums\RiskLevel|null,
     * } $attributes
     */
    public function handle(Risk $risk, array $attributes = []): Risk
    {
        $risk->update([
            ...$attributes,
            'reviewed_at' => $attributes['reviewed_at'] ?? now(),
        ]);

        RiskReviewed::dispatch($risk);

        return $risk;
    }
}
