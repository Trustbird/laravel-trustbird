<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Framework;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Frameworks\Models\FrameworkRequirement;
use Trustbird\Frameworks\Models\FrameworkVersion;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<FrameworkRequirement>
 */
final class FrameworkRequirementFactory extends Factory
{
    protected $model = FrameworkRequirement::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'framework_version_id' => FrameworkVersion::factory(),
            'code' => strtoupper($this->faker->bothify('??-##')),
            'title' => $this->faker->sentence(4),
            'summary' => $this->faker->optional()->paragraph(),
            'position' => 0,
            'metadata' => [],
        ];
    }
}
