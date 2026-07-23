<?php

declare(strict_types=1);

namespace Trustbird\Controls\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasControls
{
    public function workspace(): BelongsTo;

    public function owner(): BelongsTo;

    public function relations(): HasMany;

    public function isReviewOverdue(): bool;
}
