<?php

declare(strict_types=1);

namespace Trustbird\Assets\Actions;

use Trustbird\Assets\Events\AssetUpdated;
use Trustbird\Assets\Models\Asset;

final readonly class UpdateAsset
{
    public function handle(Asset $asset, array $attributes): Asset
    {
        $asset->update($attributes);

        AssetUpdated::dispatch($asset);

        return $asset;
    }
}
