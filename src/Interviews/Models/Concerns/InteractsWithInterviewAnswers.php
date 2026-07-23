<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Trustbird\Database\Factories\Interview\InterviewAnswerFactory;
use Trustbird\Interviews\Models\Interview;
use Trustbird\Interviews\Models\InterviewQuestion;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithInterviewAnswers
{
    use BelongsToWorkspace;

    public function initializeInteractsWithInterviewAnswers(): void
    {
        $this->mergeFillable([
            'interview_id',
            'question_id',
            'answered_by_id',
            'value',
            'notes',
            'answered_at',
            'metadata',
        ]);

        $this->mergeCasts([
            'value' => 'array',
            'answered_at' => 'datetime',
            'metadata' => 'array',
        ]);
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(InterviewQuestion::class, 'question_id');
    }

    public function answeredBy(): BelongsTo
    {
        return $this->belongsTo(app(HasPeople::class)::class, 'answered_by_id');
    }

    protected static function newFactory(): InterviewAnswerFactory
    {
        return InterviewAnswerFactory::new();
    }
}
