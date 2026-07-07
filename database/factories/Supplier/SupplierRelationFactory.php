<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Supplier;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Trustbird\Risks\Models\Risk;
use Trustbird\Suppliers\Models\Supplier;
use Trustbird\Suppliers\Models\SupplierRelation;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<SupplierRelation>
 */
final class SupplierRelationFactory extends Factory
{
    protected $model = SupplierRelation::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'supplier_id' => Supplier::factory(),
            'related_type' => Risk::class,
            'related_id' => (string) Str::ulid(),
            'metadata' => [],
        ];
    }
}

