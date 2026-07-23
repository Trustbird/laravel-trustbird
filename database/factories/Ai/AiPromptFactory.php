<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Ai;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Ai\Models\AiPrompt;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<AiPrompt>
 */
final class AiPromptFactory extends Factory
{
    protected $model = AiPrompt::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'key' => $this->faker->unique()->slug(2),
            'name' => $this->faker->sentence(3),
            'body' => 'Suggest a plain-language control based on: {{context}}',
            'purpose' => 'control_suggestion',
            'metadata' => [],
        ];
    }
}
