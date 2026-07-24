<?php

declare(strict_types=1);

namespace Trustbird\Ai\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Trustbird\Ai\Contracts\HasAiSuggestions;
use Trustbird\Ai\Enums\AiProviderDriver;
use Trustbird\Database\Factories\Ai\AiProviderFactory;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithAiProviders
{
    use BelongsToWorkspace;

    public function initializeInteractsWithAiProviders(): void
    {
        $this->mergeFillable([
            'name',
            'driver',
            'is_active',
            'settings',
            'metadata',
        ]);

        $this->mergeCasts([
            'driver' => AiProviderDriver::class,
            'is_active' => 'boolean',
            'settings' => 'array',
            'metadata' => 'array',
        ]);
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(app(HasAiSuggestions::class)::class, 'provider_id');
    }

    public function isActive(): bool
    {
        return (bool) $this->getAttribute('is_active');
    }

    protected static function newFactory(): AiProviderFactory
    {
        return AiProviderFactory::new();
    }
}
