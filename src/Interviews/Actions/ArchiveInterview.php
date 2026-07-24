<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Actions;

use InvalidArgumentException;
use Trustbird\Interviews\Contracts\HasInterviews;
use Trustbird\Interviews\Enums\InterviewStatus;
use Trustbird\Interviews\Events\InterviewArchived;

final readonly class ArchiveInterview
{
    public function handle(HasInterviews $interview): HasInterviews
    {
        if ($interview->status === InterviewStatus::Archived) {
            throw new InvalidArgumentException('This interview is already archived.');
        }

        $interview->update([
            'status' => InterviewStatus::Archived,
        ]);

        InterviewArchived::dispatch($interview);

        return $interview;
    }
}
