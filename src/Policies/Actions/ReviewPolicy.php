<?php

declare(strict_types=1);

namespace Trustbird\Policies\Actions;

use Trustbird\Policies\Events\PolicyReviewed;
use Trustbird\Policies\Models\Policy;

final readonly class ReviewPolicy
{
    /**
     * @param array{
     *     reviewed_at?: \DateTimeInterface|null,
     *     next_review_at?: \DateTimeInterface|null,
     *     reviewer_id?: string|null,
     * } $attributes
     */
    public function handle(Policy $policy, array $attributes = []): Policy
    {
        $policy->update([
            ...$attributes,
            'reviewed_at' => $attributes['reviewed_at'] ?? now(),
        ]);

        PolicyReviewed::dispatch($policy);

        return $policy;
    }
}
