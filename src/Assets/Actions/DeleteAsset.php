<?php

declare(strict_types=1);

namespace Trustbird\Assets\Actions;

use Trustbird\Assets\Events\AssetDeleted;
use Trustbird\Assets\Models\Asset;

final readonly class DeleteAsset
{
    public function handle(Asset $asset): bool
    {
        $deleted = $asset->delete();

        if ($deleted) {
            AssetDeleted::dispatch($asset);
        }

        return $deleted;
    }
}
