<?php

declare(strict_types=1);

namespace Trustbird\Suppliers\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Trustbird\Database\Factories\Supplier\SupplierFactory;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\Suppliers\Enums\SupplierCriticality;
use Trustbird\Suppliers\Enums\SupplierStatus;
use Trustbird\Suppliers\Models\SupplierRelation;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithSuppliers
{
    use BelongsToWorkspace;

    public function initializeInteractsWithSuppliers(): void
    {
        $this->mergeFillable([
            'name',
            'description',
            'status',
            'criticality',
            'owner_id',
            'reviewed_at',
            'next_review_at',
            'metadata',
        ]);

        $this->mergeCasts([
            'status' => SupplierStatus::class,
            'criticality' => SupplierCriticality::class,
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
        return $this->hasMany(SupplierRelation::class);
    }

    public function isReviewOverdue(): bool
    {
        $nextReviewAt = $this->getAttribute('next_review_at');

        if (! $nextReviewAt instanceof Carbon) {
            return false;
        }

        return $nextReviewAt->isPast();
    }

    protected static function newFactory(): SupplierFactory
    {
        return SupplierFactory::new();
    }
}

