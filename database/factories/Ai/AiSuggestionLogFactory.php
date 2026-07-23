<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Ai;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Ai\Enums\AiSuggestionLogEvent;
use Trustbird\Ai\Models\AiSuggestion;
use Trustbird\Ai\Models\AiSuggestionLog;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<AiSuggestionLog>
 */
final class AiSuggestionLogFactory extends Factory
{
    protected $model = AiSuggestionLog::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'suggestion_id' => AiSuggestion::factory(),
            'event' => AiSuggestionLogEvent::Created,
            'actor_id' => Person::factory(),
            'payload' => [],
            'metadata' => [],
        ];
    }
}
