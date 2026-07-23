<?php

declare(strict_types=1);

namespace Trustbird\Reviews\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Trustbird\Database\Factories\Review\ReviewFactory;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\Reviews\Enums\ReviewStatus;
use Trustbird\Reviews\Models\ReviewReviewer;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithReviews
{
    use BelongsToWorkspace;

    public function initializeInteractsWithReviews(): void
    {
        $this->mergeFillable([
            'reviewable_type',
            'reviewable_id',
            'status',
            'due_at',
            'completed_at',
            'reviewer_id',
            'notes',
            'metadata',
        ]);

        $this->mergeCasts([
            'status' => ReviewStatus::class,
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
            'metadata' => 'array',
        ]);
    }

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(app(HasPeople::class)::class, 'reviewer_id');
    }

    public function reviewers(): HasMany
    {
        return $this->hasMany(ReviewReviewer::class);
    }

    public function isDue(): bool
    {
        if ($this->status !== ReviewStatus::Scheduled) {
            return false;
        }

        $dueAt = $this->getAttribute('due_at');

        if (! $dueAt instanceof Carbon) {
            return false;
        }

        return $dueAt->isPast();
    }

    public function isCompleted(): bool
    {
        return $this->status === ReviewStatus::Completed;
    }

    protected static function newFactory(): ReviewFactory
    {
        return ReviewFactory::new();
    }
}
