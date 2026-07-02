<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Asset;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Assets\Enums\AssetKind;
use Trustbird\Assets\Models\Asset;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<Asset>
 */
final class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'kind' => $this->faker->randomElement(AssetKind::cases()),
            'owner_id' => Person::factory(),
            'criticality' => $this->faker->randomElement(['low', 'normal', 'high', 'critical']),
            'contains_personal_data' => $this->faker->boolean(30),
            'contains_sensitive_data' => $this->faker->boolean(10),
            'status' => 'active',
            'acquired_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'metadata' => [],
        ];
    }

    public function device(): self
    {
        return $this->state(fn (array $attributes) => [
            'kind' => AssetKind::Device,
        ]);
    }

    public function application(): self
    {
        return $this->state(fn (array $attributes) => [
            'kind' => AssetKind::Application,
        ]);
    }
}
