<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Actions;

use InvalidArgumentException;
use Trustbird\Interviews\Contracts\HasInterviews;
use Trustbird\Interviews\Enums\InterviewStatus;
use Trustbird\Interviews\Events\InterviewCompleted;

final readonly class CompleteInterview
{
    public function handle(HasInterviews $interview): HasInterviews
    {
        if ($interview->status === InterviewStatus::Completed) {
            throw new InvalidArgumentException('This interview is already completed.');
        }

        if ($interview->status === InterviewStatus::Archived) {
            throw new InvalidArgumentException('Archived interviews cannot be completed.');
        }

        $missingRequired = $interview->questions()
            ->where('is_required', true)
            ->whereDoesntHave('answer')
            ->exists();

        if ($missingRequired) {
            throw new InvalidArgumentException('All required questions must be answered before completing the interview.');
        }

        $interview->update([
            'status' => InterviewStatus::Completed,
            'completed_at' => now(),
            'started_at' => $interview->started_at ?? now(),
        ]);

        InterviewCompleted::dispatch($interview);

        return $interview;
    }
}
