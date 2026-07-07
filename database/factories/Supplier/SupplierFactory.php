<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Supplier;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\People\Models\Person;
use Trustbird\Suppliers\Enums\SupplierCriticality;
use Trustbird\Suppliers\Enums\SupplierStatus;
use Trustbird\Suppliers\Models\Supplier;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<Supplier>
 */
final class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'name' => $this->faker->company(),
            'description' => $this->faker->optional()->paragraph(),
            'status' => SupplierStatus::Active,
            'criticality' => $this->faker->randomElement(SupplierCriticality::cases()),
            'owner_id' => Person::factory(),
            'reviewed_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'next_review_at' => $this->faker->optional()->dateTimeBetween('now', '+1 year'),
            'metadata' => [],
        ];
    }

    public function active(): self
    {
        return $this->state(fn () => ['status' => SupplierStatus::Active]);
    }

    public function offboarded(): self
    {
        return $this->state(fn () => ['status' => SupplierStatus::Offboarded]);
    }
}

