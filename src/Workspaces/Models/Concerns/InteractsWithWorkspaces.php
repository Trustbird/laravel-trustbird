<?php

declare(strict_types=1);

namespace Trustbird\Workspaces\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Trustbird\Assets\Contracts\HasAssets;
use Trustbird\Database\Factories\Workspace\WorkspaceFactory;
use Trustbird\People\Contracts\HasPeople;

trait InteractsWithWorkspaces
{
    public function initializeInteractsWithWorkspaces(): void
    {
        $this->mergeFillable([
            'name',
            'slug',
            'description',
            'metadata',
        ]);

        $this->mergeCasts([
            'metadata' => 'array',
        ]);
    }

    protected static function newFactory(): WorkspaceFactory
    {
        return WorkspaceFactory::new();
    }

    public function people(): HasMany
    {
        return $this->hasMany(app(HasPeople::class)::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(app(HasAssets::class)::class);
    }
}
