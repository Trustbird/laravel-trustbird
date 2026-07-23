<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Trustbird\Database\Factories\Framework\FrameworkFactory;
use Trustbird\Frameworks\Models\FrameworkVersion;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithFrameworks
{
    use BelongsToWorkspace;

    public function initializeInteractsWithFrameworks(): void
    {
        $this->mergeFillable([
            'name',
            'description',
            'slug',
            'owner_id',
            'current_version_id',
            'metadata',
        ]);

        $this->mergeCasts([
            'metadata' => 'array',
        ]);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(app(HasPeople::class)::class, 'owner_id');
    }

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(FrameworkVersion::class, 'current_version_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(FrameworkVersion::class);
    }

    public function hasPublishedVersion(): bool
    {
        return $this->current_version_id !== null;
    }

    protected static function newFactory(): FrameworkFactory
    {
        return FrameworkFactory::new();
    }
}
