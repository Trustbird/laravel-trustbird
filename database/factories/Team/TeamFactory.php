<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Team;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\People\Models\Person;
use Trustbird\Teams\Models\Team;
use Trustbird\Workspaces\Models\Workspace;

final class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'name' => fake()->company(),
            'description' => fake()->sentence(),
            'owner_id' => Person::factory(),
        ];
    }
}
