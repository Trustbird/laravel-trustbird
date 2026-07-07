<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Control;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Trustbird\Controls\Models\Control;
use Trustbird\Controls\Models\ControlRelation;
use Trustbird\Risks\Models\Risk;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<ControlRelation>
 */
final class ControlRelationFactory extends Factory
{
    protected $model = ControlRelation::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'control_id' => Control::factory(),
            'related_type' => Risk::class,
            'related_id' => (string) Str::ulid(),
            'metadata' => [],
        ];
    }
}
