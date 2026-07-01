<?php

declare(strict_types=1);

namespace Trustbird\Assets\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trustbird\Assets\Models\Asset;

class AssetCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Asset $asset,
    ) {}
}
