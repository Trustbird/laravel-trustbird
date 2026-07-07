<?php

declare(strict_types=1);

namespace Trustbird\Documents\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Trustbird\Database\Factories\Document\DocumentFactory;
use Trustbird\Documents\Models\DocumentVersion;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithDocuments
{
    use BelongsToWorkspace;

    public function initializeInteractsWithDocuments(): void
    {
        $this->mergeFillable([
            'title',
            'description',
            'owner_id',
            'reviewer_id',
            'current_version_id',
            'reviewed_at',
            'next_review_at',
            'metadata',
        ]);

        $this->mergeCasts([
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

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class, 'current_version_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function needsReview(): bool
    {
        if ($this->next_review_at === null) {
            return true;
        }

        return $this->next_review_at->isPast();
    }

    protected static function newFactory(): DocumentFactory
    {
        return DocumentFactory::new();
    }
}
