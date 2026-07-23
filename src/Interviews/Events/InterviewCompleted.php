<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trustbird\Interviews\Contracts\HasInterviews;

final class InterviewCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public HasInterviews $interview,
    ) {}
}
