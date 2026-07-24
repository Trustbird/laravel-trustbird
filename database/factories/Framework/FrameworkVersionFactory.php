<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Framework;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Frameworks\Enums\FrameworkVersionStatus;
use Trustbird\Frameworks\Models\Framework;
use Trustbird\Frameworks\Models\FrameworkVersion;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<FrameworkVersion>
 */
final class FrameworkVersionFactory extends Factory
{
    protected $model = FrameworkVersion::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'framework_id' => Framework::factory(),
            'version_label' => '1.0',
            'status' => FrameworkVersionStatus::Draft,
            'change_summary' => $this->faker->optional()->sentence(),
            'metadata' => [],
        ];
    }
}
