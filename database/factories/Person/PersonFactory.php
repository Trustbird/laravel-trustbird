<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Person;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\People\Enums\EmploymentStatus;
use Trustbird\People\Enums\EmploymentType;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Models\Workspace;

final class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),

            'email' => fake()->safeEmail(),

            'employment_type' => EmploymentType::Employee,

            'employment_status' => EmploymentStatus::Active,

            'started_at' => now(),
        ];
    }

    public function terminated(): static
    {
        return $this->state([
            'employment_status' => EmploymentStatus::Terminated,
            'ended_at' => now(),
        ]);
    }
}