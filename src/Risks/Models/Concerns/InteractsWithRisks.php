<?php

declare(strict_types=1);

namespace Trustbird\Risks\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Trustbird\Database\Factories\Risk\RiskFactory;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\Risks\Enums\RiskLevel;
use Trustbird\Risks\Enums\RiskStatus;
use Trustbird\Risks\Enums\RiskTreatment;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithRisks
{
    use BelongsToWorkspace;

    public function initializeInteractsWithRisks(): void
    {
        $this->attributes['status'] = $this->attributes['status'] ?? 'open';

        $this->mergeFillable([
            'title',
            'description',
            'owner_id',
            'status',
            'treatment',
            'likelihood',
            'impact',
            'reviewed_at',
            'next_review_at',
            'metadata',
        ]);

        $this->mergeCasts([
            'status' => RiskStatus::class,
            'treatment' => RiskTreatment::class,
            'likelihood' => RiskLevel::class,
            'impact' => RiskLevel::class,
            'reviewed_at' => 'datetime',
            'next_review_at' => 'datetime',
            'metadata' => 'array',
        ]);
    }

    protected static function newFactory(): RiskFactory
    {
        return RiskFactory::new();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(app(HasPeople::class)::class, 'owner_id');
    }

    public function isResolved(): bool
    {
        return $this->status === RiskStatus::Resolved;
    }

    public function isArchived(): bool
    {
        return $this->status === RiskStatus::Archived;
    }

    public function isActive(): bool
    {
        return ! $this->isResolved() && ! $this->isArchived();
    }

    public function needsReview(): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        if ($this->next_review_at === null) {
            return true;
        }

        return $this->next_review_at->isPast();
    }
}
