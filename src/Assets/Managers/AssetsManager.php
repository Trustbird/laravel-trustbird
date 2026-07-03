<?php

declare(strict_types=1);

namespace Trustbird\Assets\Managers;

use DateTimeInterface;
use Trustbird\Assets\Contracts\HasAssets;
use Trustbird\Assets\Enums\AssetKind;

final readonly class AssetsManager
{
    public function create(
        string $name,
        AssetKind $kind,
        ?string $description = null,
        ?string $ownerId = null,
        ?string $providerName = null,
        ?string $externalReference = null,
        ?string $environment = null,
        string $criticality = 'normal',
        bool $containsPersonalData = false,
        bool $containsSensitiveData = false,
        string $status = 'active',
        ?DateTimeInterface $acquiredAt = null,
        array $metadata = [],
        ?string $workspaceId = null,
    ): HasAssets {
        /** @var HasAssets $model */
        $model = app(HasAssets::class);

        return $model->query()->create([
            'name' => $name,
            'kind' => $kind,
            'description' => $description,
            'owner_id' => $ownerId,
            'provider_name' => $providerName,
            'external_reference' => $externalReference,
            'environment' => $environment,
            'criticality' => $criticality,
            'contains_personal_data' => $containsPersonalData,
            'contains_sensitive_data' => $containsSensitiveData,
            'status' => $status,
            'acquired_at' => $acquiredAt,
            'metadata' => $metadata,
            'workspace_id' => $workspaceId,
        ]);
    }

    public function update(
        HasAssets $asset,
        ?string $name = null,
        ?AssetKind $kind = null,
        ?string $description = null,
        ?string $ownerId = null,
        ?string $providerName = null,
        ?string $externalReference = null,
        ?string $environment = null,
        ?string $criticality = null,
        ?bool $containsPersonalData = null,
        ?bool $containsSensitiveData = null,
        ?string $status = null,
        ?DateTimeInterface $acquiredAt = null,
        ?DateTimeInterface $retiredAt = null,
        ?array $metadata = null,
    ): HasAssets {
        $attributes = array_filter([
            'name' => $name,
            'kind' => $kind,
            'description' => $description,
            'owner_id' => $ownerId,
            'provider_name' => $providerName,
            'external_reference' => $externalReference,
            'environment' => $environment,
            'criticality' => $criticality,
            'contains_personal_data' => $containsPersonalData,
            'contains_sensitive_data' => $containsSensitiveData,
            'status' => $status,
            'acquired_at' => $acquiredAt,
            'retired_at' => $retiredAt,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $asset->update($attributes);

        return $asset;
    }

    public function retire(HasAssets $asset): HasAssets
    {
        return $this->update($asset, retiredAt: now());
    }

    public function delete(HasAssets $asset): bool
    {
        return $asset->delete();
    }
}
