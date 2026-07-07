<?php

declare(strict_types=1);

namespace Trustbird\Controls\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface HasControlRelations
{
    public function workspace(): BelongsTo;

    public function control(): BelongsTo;

    public function related(): MorphTo;
}
