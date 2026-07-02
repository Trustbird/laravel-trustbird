<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Workspace;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<Workspace>
 */
final class WorkspaceFactory extends Factory
{
    protected $model = Workspace::class;

    public function definition(): array
    {
        $name = $this->faker->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'metadata' => [],
        ];
    }
}
