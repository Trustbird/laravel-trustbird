<?php

declare(strict_types=1);

namespace Trustbird\Ai\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Trustbird\Ai\Contracts\HasAiSuggestions;
use Trustbird\Ai\Enums\AiSuggestionLogEvent;
use Trustbird\Database\Factories\Ai\AiSuggestionLogFactory;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithAiSuggestionLogs
{
    use BelongsToWorkspace;

    public function initializeInteractsWithAiSuggestionLogs(): void
    {
        $this->mergeFillable([
            'suggestion_id',
            'event',
            'actor_id',
            'payload',
            'metadata',
        ]);

        $this->mergeCasts([
            'event' => AiSuggestionLogEvent::class,
            'payload' => 'array',
            'metadata' => 'array',
        ]);
    }

    public function suggestion(): BelongsTo
    {
        return $this->belongsTo(app(HasAiSuggestions::class)::class, 'suggestion_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(app(HasPeople::class)::class, 'actor_id');
    }

    protected static function newFactory(): AiSuggestionLogFactory
    {
        return AiSuggestionLogFactory::new();
    }
}
