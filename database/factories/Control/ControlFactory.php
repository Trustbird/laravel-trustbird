<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Control;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Controls\Enums\ControlStatus;
use Trustbird\Controls\Models\Control;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<Control>
 */
final class ControlFactory extends Factory
{
    protected $model = Control::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'status' => ControlStatus::Draft,
            'owner_id' => Person::factory(),
            'reviewed_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'next_review_at' => $this->faker->optional()->dateTimeBetween('now', '+1 year'),
            'metadata' => [],
        ];
    }

    public function active(): self
    {
        return $this->state(fn () => ['status' => ControlStatus::Active]);
    }
}
