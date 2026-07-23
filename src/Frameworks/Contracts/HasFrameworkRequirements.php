<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasFrameworkRequirements
{
    public function workspace(): BelongsTo;

    public function version(): BelongsTo;

    public function mappings(): HasMany;
}
