<?php

declare(strict_types=1);

namespace Trustbird\Ai\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Trustbird\Ai\Enums\AiSuggestionKind;
use Trustbird\Ai\Enums\AiSuggestionStatus;
use Trustbird\Ai\Models\AiPrompt;
use Trustbird\Ai\Models\AiProvider;
use Trustbird\Ai\Models\AiSuggestionLog;
use Trustbird\Database\Factories\Ai\AiSuggestionFactory;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithAiSuggestions
{
    use BelongsToWorkspace;

    public function initializeInteractsWithAiSuggestions(): void
    {
        $this->mergeFillable([
            'provider_id',
            'prompt_id',
            'kind',
            'status',
            'subject_type',
            'subject_id',
            'title',
            'input',
            'output',
            'model_name',
            'provider_reference',
            'created_by_id',
            'reviewed_by_id',
            'reviewed_at',
            'review_notes',
            'metadata',
        ]);

        $this->mergeCasts([
            'kind' => AiSuggestionKind::class,
            'status' => AiSuggestionStatus::class,
            'input' => 'array',
            'output' => 'array',
            'reviewed_at' => 'datetime',
            'metadata' => 'array',
        ]);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'provider_id');
    }

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(AiPrompt::class, 'prompt_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(app(HasPeople::class)::class, 'created_by_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(app(HasPeople::class)::class, 'reviewed_by_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AiSuggestionLog::class, 'suggestion_id');
    }

    public function isPending(): bool
    {
        return $this->status === AiSuggestionStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === AiSuggestionStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->status === AiSuggestionStatus::Rejected;
    }

    protected static function newFactory(): AiSuggestionFactory
    {
        return AiSuggestionFactory::new();
    }
}
