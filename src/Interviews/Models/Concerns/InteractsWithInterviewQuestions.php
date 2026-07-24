<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Trustbird\Database\Factories\Interview\InterviewQuestionFactory;
use Trustbird\Interviews\Contracts\HasInterviewAnswers;
use Trustbird\Interviews\Contracts\HasInterviews;
use Trustbird\Interviews\Enums\InterviewQuestionType;
use Trustbird\Interviews\Enums\InterviewSuggestionDomain;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithInterviewQuestions
{
    use BelongsToWorkspace;

    public function initializeInteractsWithInterviewQuestions(): void
    {
        $this->mergeFillable([
            'interview_id',
            'position',
            'prompt',
            'help_text',
            'type',
            'options',
            'suggestion_domain',
            'suggestion_key',
            'is_required',
            'metadata',
        ]);

        $this->mergeCasts([
            'type' => InterviewQuestionType::class,
            'suggestion_domain' => InterviewSuggestionDomain::class,
            'options' => 'array',
            'is_required' => 'boolean',
            'metadata' => 'array',
        ]);
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(app(HasInterviews::class)::class);
    }

    public function answer(): HasOne
    {
        return $this->hasOne(app(HasInterviewAnswers::class)::class, 'question_id');
    }

    protected static function newFactory(): InterviewQuestionFactory
    {
        return InterviewQuestionFactory::new();
    }
}
