<?php

declare(strict_types=1);

namespace Trustbird\Suppliers\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface HasSupplierRelations
{
    public function workspace(): BelongsTo;

    public function supplier(): BelongsTo;

    public function related(): MorphTo;
}

