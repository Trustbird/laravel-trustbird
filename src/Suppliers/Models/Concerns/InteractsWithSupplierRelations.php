<?php

declare(strict_types=1);

namespace Trustbird\Suppliers\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Trustbird\Database\Factories\Supplier\SupplierRelationFactory;
use Trustbird\Suppliers\Models\Supplier;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithSupplierRelations
{
    use BelongsToWorkspace;

    public function initializeInteractsWithSupplierRelations(): void
    {
        $this->mergeFillable([
            'supplier_id',
            'related_type',
            'related_id',
            'metadata',
        ]);

        $this->mergeCasts([
            'metadata' => 'array',
        ]);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function newFactory(): SupplierRelationFactory
    {
        return SupplierRelationFactory::new();
    }
}

