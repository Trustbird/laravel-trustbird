<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Trustbird\Database\Factories\Framework\FrameworkMappingFactory;
use Trustbird\Frameworks\Contracts\HasFrameworkRequirements;
use Trustbird\Frameworks\Enums\FrameworkMappingCoverage;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithFrameworkMappings
{
    use BelongsToWorkspace;

    public function initializeInteractsWithFrameworkMappings(): void
    {
        $this->mergeFillable([
            'requirement_id',
            'related_type',
            'related_id',
            'coverage',
            'metadata',
        ]);

        $this->mergeCasts([
            'coverage' => FrameworkMappingCoverage::class,
            'metadata' => 'array',
        ]);
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(app(HasFrameworkRequirements::class)::class, 'requirement_id');
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function newFactory(): FrameworkMappingFactory
    {
        return FrameworkMappingFactory::new();
    }
}
