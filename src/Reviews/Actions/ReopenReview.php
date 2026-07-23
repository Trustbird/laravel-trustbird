<?php

declare(strict_types=1);

namespace Trustbird\Reviews\Actions;

use InvalidArgumentException;
use Trustbird\Reviews\Contracts\HasReviews;
use Trustbird\Reviews\Enums\ReviewStatus;
use Trustbird\Reviews\Events\ReviewReopened;

final readonly class ReopenReview
{
    public function handle(HasReviews $review): HasReviews
    {
        if ($review->status !== ReviewStatus::Completed) {
            throw new InvalidArgumentException('Only completed reviews can be reopened.');
        }

        $review->update([
            'status' => ReviewStatus::Reopened,
        ]);

        ReviewReopened::dispatch($review);

        return $review;
    }
}
