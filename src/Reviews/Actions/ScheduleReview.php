<?php

declare(strict_types=1);

namespace Trustbird\Reviews\Actions;

use Trustbird\Reviews\Contracts\HasReviews;
use Trustbird\Reviews\Enums\ReviewStatus;
use Trustbird\Reviews\Events\ReviewScheduled;

final readonly class ScheduleReview
{
    /**
     * @param array{
     *     due_at?: \DateTimeInterface|null,
     *     reviewer_id?: string|null,
     *     metadata?: array|null,
     * } $attributes
     */
    public function handle(object $subject, array $attributes = []): HasReviews
    {
        /** @var HasReviews $model */
        $model = app(HasReviews::class);

        $review = $model->query()->create([
            'workspace_id' => $subject->workspace_id ?? null,
            'reviewable_type' => $subject::class,
            'reviewable_id' => $subject->id,
            'status' => ReviewStatus::Scheduled,
            'due_at' => $attributes['due_at'] ?? null,
            'reviewer_id' => $attributes['reviewer_id'] ?? null,
            'metadata' => $attributes['metadata'] ?? null,
        ]);

        ReviewScheduled::dispatch($review);

        return $review;
    }
}
