<?php

declare(strict_types=1);

namespace Trustbird\Reviews\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trustbird\Reviews\Contracts\HasReviews;

final class ReviewCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public HasReviews $review,
    ) {}
}
