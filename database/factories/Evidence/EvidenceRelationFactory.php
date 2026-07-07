<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Evidence;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Trustbird\Controls\Models\Control;
use Trustbird\Evidence\Models\Evidence;
use Trustbird\Evidence\Models\EvidenceRelation;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<EvidenceRelation>
 */
final class EvidenceRelationFactory extends Factory
{
    protected $model = EvidenceRelation::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'evidence_id' => Evidence::factory(),
            'related_type' => Control::class,
            'related_id' => (string) Str::ulid(),
            'metadata' => [],
        ];
    }
}
