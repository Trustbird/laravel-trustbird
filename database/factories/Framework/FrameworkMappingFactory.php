<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Framework;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Trustbird\Controls\Models\Control;
use Trustbird\Frameworks\Enums\FrameworkMappingCoverage;
use Trustbird\Frameworks\Models\FrameworkMapping;
use Trustbird\Frameworks\Models\FrameworkRequirement;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<FrameworkMapping>
 */
final class FrameworkMappingFactory extends Factory
{
    protected $model = FrameworkMapping::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'requirement_id' => FrameworkRequirement::factory(),
            'related_type' => Control::class,
            'related_id' => (string) Str::ulid(),
            'coverage' => FrameworkMappingCoverage::Planned,
            'metadata' => [],
        ];
    }
}
