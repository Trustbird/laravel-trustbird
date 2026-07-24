<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasFrameworks
{
    public function workspace(): BelongsTo;

    public function owner(): BelongsTo;

    public function currentVersion(): BelongsTo;

    public function versions(): HasMany;

    public function hasPublishedVersion(): bool;
}
