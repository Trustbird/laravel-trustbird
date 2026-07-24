<?php

declare(strict_types=1);

namespace Trustbird\Ai\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasAiPrompts
{
    public function workspace(): BelongsTo;

    public function suggestions(): HasMany;
}
