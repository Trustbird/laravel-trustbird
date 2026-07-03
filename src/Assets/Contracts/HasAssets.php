<?php

declare(strict_types=1);

namespace Trustbird\Assets\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasAssets
{
    public function owner(): BelongsTo;

    public function workspace(): BelongsTo;

    public function isDevice(): bool;

    public function isSystem(): bool;

    public function isDataCarrier(): bool;
}
