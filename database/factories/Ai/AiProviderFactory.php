<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Ai;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Ai\Enums\AiProviderDriver;
use Trustbird\Ai\Models\AiProvider;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<AiProvider>
 */
final class AiProviderFactory extends Factory
{
    protected $model = AiProvider::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'name' => $this->faker->company().' AI',
            'driver' => AiProviderDriver::Custom,
            'is_active' => true,
            'settings' => ['region' => 'eu'],
            'metadata' => [],
        ];
    }

    public function inactive(): self
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
