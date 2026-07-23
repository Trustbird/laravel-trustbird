<?php

declare(strict_types=1);

namespace Trustbird\Reviews\Actions;

use InvalidArgumentException;
use Trustbird\Reviews\Contracts\HasReviews;
use Trustbird\Reviews\Enums\ReviewStatus;
use Trustbird\Reviews\Events\ReviewCompleted;

final readonly class CompleteReview
{
    /**
     * @param array{
     *     completed_at?: \DateTimeInterface|null,
     *     reviewer_id?: string|null,
     *     notes?: string|null,
     *     metadata?: array|null,
     * } $attributes
     */
    public function handle(HasReviews $review, array $attributes = []): HasReviews
    {
        if ($review->status === ReviewStatus::Completed) {
            throw new InvalidArgumentException('This review is already completed.');
        }

        $review->update([
            'status' => ReviewStatus::Completed,
            'completed_at' => $attributes['completed_at'] ?? now(),
            'reviewer_id' => $attributes['reviewer_id'] ?? $review->reviewer_id,
            'notes' => $attributes['notes'] ?? $review->notes,
            'metadata' => $attributes['metadata'] ?? $review->metadata,
        ]);

        ReviewCompleted::dispatch($review);

        return $review;
    }
}
