<?php

declare(strict_types=1);

namespace Trustbird\Evidence\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Trustbird\Database\Factories\Evidence\EvidenceRelationFactory;
use Trustbird\Evidence\Models\Evidence;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithEvidenceRelations
{
    use BelongsToWorkspace;

    public function initializeInteractsWithEvidenceRelations(): void
    {
        $this->mergeFillable([
            'evidence_id',
            'related_type',
            'related_id',
            'metadata',
        ]);

        $this->mergeCasts([
            'metadata' => 'array',
        ]);
    }

    public function evidence(): BelongsTo
    {
        return $this->belongsTo(Evidence::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function newFactory(): EvidenceRelationFactory
    {
        return EvidenceRelationFactory::new();
    }
}
