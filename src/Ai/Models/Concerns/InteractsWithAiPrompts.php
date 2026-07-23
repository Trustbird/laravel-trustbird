<?php

declare(strict_types=1);

namespace Trustbird\Ai\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Trustbird\Ai\Models\AiSuggestion;
use Trustbird\Database\Factories\Ai\AiPromptFactory;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithAiPrompts
{
    use BelongsToWorkspace;

    public function initializeInteractsWithAiPrompts(): void
    {
        $this->mergeFillable([
            'key',
            'name',
            'body',
            'purpose',
            'metadata',
        ]);

        $this->mergeCasts([
            'metadata' => 'array',
        ]);
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(AiSuggestion::class, 'prompt_id');
    }

    protected static function newFactory(): AiPromptFactory
    {
        return AiPromptFactory::new();
    }
}
