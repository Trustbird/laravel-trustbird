<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Ai;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Ai\Enums\AiSuggestionKind;
use Trustbird\Ai\Enums\AiSuggestionStatus;
use Trustbird\Ai\Models\AiPrompt;
use Trustbird\Ai\Models\AiProvider;
use Trustbird\Ai\Models\AiSuggestion;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<AiSuggestion>
 */
final class AiSuggestionFactory extends Factory
{
    protected $model = AiSuggestion::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'provider_id' => AiProvider::factory(),
            'prompt_id' => AiPrompt::factory(),
            'kind' => AiSuggestionKind::General,
            'status' => AiSuggestionStatus::Pending,
            'title' => $this->faker->sentence(4),
            'input' => ['context' => 'demo'],
            'output' => ['text' => $this->faker->paragraph()],
            'model_name' => 'demo-model',
            'created_by_id' => Person::factory(),
            'metadata' => [],
        ];
    }

    public function approved(): self
    {
        return $this->state(fn () => [
            'status' => AiSuggestionStatus::Approved,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): self
    {
        return $this->state(fn () => [
            'status' => AiSuggestionStatus::Rejected,
            'reviewed_at' => now(),
        ]);
    }
}
