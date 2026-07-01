<?php

declare(strict_types=1);

namespace Trustbird\Assets\Actions;

use Trustbird\Assets\Events\AssetCreated;
use Trustbird\Assets\Models\Asset;

final readonly class CreateAsset
{
    /**
     * @param array{
     *     name: string,
     *     type: string|\Trustbird\Assets\Enums\AssetKind,
     *     description?: string|null,
     *     owner_id?: string|null,
     *     serial_number?: string|null,
     *     model_number?: string|null,
     *     manufacturer?: string|null,
     *     is_critical?: bool,
     *     contains_user_data?: bool,
     *     status?: string,
     *     acquired_at?: \DateTimeInterface|null,
     *     metadata?: array|null,
     * } $attributes
     */
    public function handle(array $attributes): Asset
    {
        $asset = Asset::query()->create($attributes);

        AssetCreated::dispatch($asset);

        return $asset;
    }
}
