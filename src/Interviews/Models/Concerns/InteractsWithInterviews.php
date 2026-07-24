<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Trustbird\Database\Factories\Interview\InterviewFactory;
use Trustbird\Interviews\Contracts\HasInterviewAnswers;
use Trustbird\Interviews\Contracts\HasInterviewQuestions;
use Trustbird\Interviews\Enums\InterviewStatus;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithInterviews
{
    use BelongsToWorkspace;

    public function initializeInteractsWithInterviews(): void
    {
        $this->mergeFillable([
            'title',
            'description',
            'status',
            'owner_id',
            'answered_count',
            'question_count',
            'started_at',
            'completed_at',
            'metadata',
        ]);

        $this->mergeCasts([
            'status' => InterviewStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'metadata' => 'array',
        ]);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(app(HasPeople::class)::class, 'owner_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(app(HasInterviewQuestions::class)::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(app(HasInterviewAnswers::class)::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === InterviewStatus::Completed;
    }

    public function isInProgress(): bool
    {
        return $this->status === InterviewStatus::InProgress;
    }

    public function progressPercent(): int
    {
        $questionCount = (int) $this->getAttribute('question_count');

        if ($questionCount === 0) {
            return 0;
        }

        $answeredCount = (int) $this->getAttribute('answered_count');

        return (int) min(100, (int) round(($answeredCount / $questionCount) * 100));
    }

    protected static function newFactory(): InterviewFactory
    {
        return InterviewFactory::new();
    }
}
