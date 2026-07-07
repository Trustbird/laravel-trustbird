<?php

declare(strict_types=1);

namespace Trustbird\Reviews\Managers;

use DateTimeInterface;
use Trustbird\Reviews\Actions\CompleteReview;
use Trustbird\Reviews\Actions\ReopenReview;
use Trustbird\Reviews\Actions\ScheduleReview;
use Trustbird\Reviews\Contracts\HasReviewReviewers;
use Trustbird\Reviews\Contracts\HasReviews;
use Trustbird\Reviews\Enums\ReviewerRole;
use Trustbird\Reviews\Models\Review;

final readonly class ReviewsManager
{
    public function schedule(
        object $subject,
        ?DateTimeInterface $dueAt = null,
        ?string $reviewerId = null,
        array $metadata = [],
    ): HasReviews {
        return app(ScheduleReview::class)->handle($subject, array_filter([
            'due_at' => $dueAt,
            'reviewer_id' => $reviewerId,
            'metadata' => $metadata ?: null,
        ], fn ($value) => $value !== null));
    }

    public function complete(
        HasReviews $review,
        ?DateTimeInterface $completedAt = null,
        ?string $reviewerId = null,
        ?string $notes = null,
        ?array $metadata = null,
    ): HasReviews {
        return app(CompleteReview::class)->handle($review, array_filter([
            'completed_at' => $completedAt,
            'reviewer_id' => $reviewerId,
            'notes' => $notes,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null));
    }

    public function reopen(HasReviews $review): HasReviews
    {
        return app(ReopenReview::class)->handle($review);
    }

    public function assignReviewer(
        Review $review,
        string $personId,
        ReviewerRole $role = ReviewerRole::Primary,
        array $metadata = [],
    ): HasReviewReviewers {
        /** @var HasReviewReviewers $model */
        $model = app(HasReviewReviewers::class);

        return $model->query()->create([
            'workspace_id' => $review->workspace_id,
            'review_id' => $review->id,
            'person_id' => $personId,
            'role' => $role,
            'metadata' => $metadata,
        ]);
    }
}
