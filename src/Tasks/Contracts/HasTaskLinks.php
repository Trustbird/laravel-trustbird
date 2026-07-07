<?php

declare(strict_types=1);

namespace Trustbird\Tasks\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface HasTaskLinks
{
    public function workspace(): BelongsTo;

    public function task(): BelongsTo;

    public function related(): MorphTo;
}

