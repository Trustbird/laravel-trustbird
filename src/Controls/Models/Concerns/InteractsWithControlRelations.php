<?php

declare(strict_types=1);

namespace Trustbird\Controls\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Trustbird\Controls\Models\Control;
use Trustbird\Database\Factories\Control\ControlRelationFactory;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithControlRelations
{
    use BelongsToWorkspace;

    public function initializeInteractsWithControlRelations(): void
    {
        $this->mergeFillable([
            'control_id',
            'related_type',
            'related_id',
            'metadata',
        ]);

        $this->mergeCasts([
            'metadata' => 'array',
        ]);
    }

    public function control(): BelongsTo
    {
        return $this->belongsTo(Control::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function newFactory(): ControlRelationFactory
    {
        return ControlRelationFactory::new();
    }
}
