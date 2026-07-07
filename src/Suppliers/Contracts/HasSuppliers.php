<?php

declare(strict_types=1);

namespace Trustbird\Suppliers\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasSuppliers
{
    public function workspace(): BelongsTo;

    public function owner(): BelongsTo;

    public function relations(): HasMany;

    public function isReviewOverdue(): bool;
}

