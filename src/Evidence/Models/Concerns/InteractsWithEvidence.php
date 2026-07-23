<?php

declare(strict_types=1);

namespace Trustbird\Evidence\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Trustbird\Database\Factories\Evidence\EvidenceFactory;
use Trustbird\Evidence\Enums\EvidenceStatus;
use Trustbird\Evidence\Enums\EvidenceType;
use Trustbird\Evidence\Models\EvidenceRelation;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithEvidence
{
    use BelongsToWorkspace;

    public function initializeInteractsWithEvidence(): void
    {
        $this->mergeFillable([
            'title',
            'description',
            'type',
            'status',
            'owner_id',
            'reviewer_id',
            'reviewed_at',
            'next_review_at',
            'external_url',
            'storage_key',
            'metadata',
        ]);

        $this->mergeCasts([
            'type' => EvidenceType::class,
            'status' => EvidenceStatus::class,
            'reviewed_at' => 'datetime',
            'next_review_at' => 'datetime',
            'metadata' => 'array',
        ]);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(app(HasPeople::class)::class, 'owner_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(app(HasPeople::class)::class, 'reviewer_id');
    }

    public function relations(): HasMany
    {
        return $this->hasMany(EvidenceRelation::class);
    }

    public function isReviewOverdue(): bool
    {
        $nextReviewAt = $this->getAttribute('next_review_at');

        if (! $nextReviewAt instanceof Carbon) {
            return false;
        }

        return $nextReviewAt->isPast();
    }

    protected static function newFactory(): EvidenceFactory
    {
        return EvidenceFactory::new();
    }
}
