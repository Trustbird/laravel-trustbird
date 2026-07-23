<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Trustbird\Database\Factories\Framework\FrameworkRequirementFactory;
use Trustbird\Frameworks\Models\FrameworkMapping;
use Trustbird\Frameworks\Models\FrameworkVersion;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithFrameworkRequirements
{
    use BelongsToWorkspace;

    public function initializeInteractsWithFrameworkRequirements(): void
    {
        $this->mergeFillable([
            'framework_version_id',
            'code',
            'title',
            'summary',
            'position',
            'metadata',
        ]);

        $this->mergeCasts([
            'metadata' => 'array',
        ]);
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(FrameworkVersion::class, 'framework_version_id');
    }

    public function mappings(): HasMany
    {
        return $this->hasMany(FrameworkMapping::class, 'requirement_id');
    }

    protected static function newFactory(): FrameworkRequirementFactory
    {
        return FrameworkRequirementFactory::new();
    }
}
