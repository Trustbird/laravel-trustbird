<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface HasFrameworkMappings
{
    public function workspace(): BelongsTo;

    public function requirement(): BelongsTo;

    public function related(): MorphTo;
}
