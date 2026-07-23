<?php

declare(strict_types=1);

namespace Trustbird\Controls\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Trustbird\Controls\Enums\ControlStatus;
use Trustbird\Controls\Models\ControlRelation;
use Trustbird\Database\Factories\Control\ControlFactory;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithControls
{
    use BelongsToWorkspace;

    public function initializeInteractsWithControls(): void
    {
        $this->mergeFillable([
            'name',
            'description',
            'status',
            'owner_id',
            'reviewed_at',
            'next_review_at',
            'metadata',
        ]);

        $this->mergeCasts([
            'status' => ControlStatus::class,
            'reviewed_at' => 'datetime',
            'next_review_at' => 'datetime',
            'metadata' => 'array',
        ]);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(app(HasPeople::class)::class, 'owner_id');
    }

    public function relations(): HasMany
    {
        return $this->hasMany(ControlRelation::class);
    }

    public function isReviewOverdue(): bool
    {
        $nextReviewAt = $this->getAttribute('next_review_at');

        if (! $nextReviewAt instanceof Carbon) {
            return false;
        }

        return $nextReviewAt->isPast();
    }

    protected static function newFactory(): ControlFactory
    {
        return ControlFactory::new();
    }
}
