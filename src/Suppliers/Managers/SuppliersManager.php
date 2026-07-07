<?php

declare(strict_types=1);

namespace Trustbird\Suppliers\Managers;

use DateTimeInterface;
use Trustbird\Suppliers\Contracts\HasSupplierRelations;
use Trustbird\Suppliers\Contracts\HasSuppliers;
use Trustbird\Suppliers\Enums\SupplierCriticality;
use Trustbird\Suppliers\Enums\SupplierStatus;
use Trustbird\Suppliers\Models\Supplier;

final readonly class SuppliersManager
{
    public function create(
        string $name,
        ?string $description = null,
        SupplierStatus $status = SupplierStatus::Active,
        SupplierCriticality $criticality = SupplierCriticality::Medium,
        ?string $ownerId = null,
        ?DateTimeInterface $reviewedAt = null,
        ?DateTimeInterface $nextReviewAt = null,
        array $metadata = [],
        ?string $workspaceId = null,
    ): HasSuppliers {
        /** @var HasSuppliers $model */
        $model = app(HasSuppliers::class);

        return $model->query()->create([
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'criticality' => $criticality,
            'owner_id' => $ownerId,
            'reviewed_at' => $reviewedAt,
            'next_review_at' => $nextReviewAt,
            'metadata' => $metadata,
            'workspace_id' => $workspaceId,
        ]);
    }

    public function update(
        HasSuppliers $supplier,
        ?string $name = null,
        ?string $description = null,
        ?SupplierStatus $status = null,
        ?SupplierCriticality $criticality = null,
        ?string $ownerId = null,
        ?DateTimeInterface $reviewedAt = null,
        ?DateTimeInterface $nextReviewAt = null,
        ?array $metadata = null,
    ): HasSuppliers {
        $attributes = array_filter([
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'criticality' => $criticality,
            'owner_id' => $ownerId,
            'reviewed_at' => $reviewedAt,
            'next_review_at' => $nextReviewAt,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $supplier->update($attributes);

        return $supplier;
    }

    public function review(
        HasSuppliers $supplier,
        ?DateTimeInterface $reviewedAt = null,
        ?DateTimeInterface $nextReviewAt = null,
    ): HasSuppliers {
        return $this->update(
            supplier: $supplier,
            reviewedAt: $reviewedAt ?? now(),
            nextReviewAt: $nextReviewAt,
        );
    }

    public function relate(
        Supplier $supplier,
        object $related,
        array $metadata = [],
    ): HasSupplierRelations {
        /** @var HasSupplierRelations $model */
        $model = app(HasSupplierRelations::class);

        return $model->query()->create([
            'workspace_id' => $supplier->workspace_id,
            'supplier_id' => $supplier->id,
            'related_type' => $related::class,
            'related_id' => $related->id,
            'metadata' => $metadata,
        ]);
    }
}

