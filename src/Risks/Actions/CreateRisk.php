<?php

declare(strict_types=1);

namespace Trustbird\Risks\Actions;

use Trustbird\Risks\Events\RiskCreated;
use Trustbird\Risks\Models\Risk;

final readonly class CreateRisk
{
    /**
     * @param array{
     *     title: string,
     *     description?: string|null,
     *     owner_id?: string|null,
     *     status?: string|\Trustbird\Risks\Enums\RiskStatus,
     *     treatment?: string|\Trustbird\Risks\Enums\RiskTreatment|null,
     *     likelihood?: string|\Trustbird\Risks\Enums\RiskLevel|null,
     *     impact?: string|\Trustbird\Risks\Enums\RiskLevel|null,
     *     next_review_at?: \DateTimeInterface|null,
     *     metadata?: array|null,
     * } $attributes
     */
    public function handle(array $attributes): Risk
    {
        $risk = Risk::query()->create($attributes);

        RiskCreated::dispatch($risk);

        return $risk;
    }
}
