<?php

declare(strict_types=1);

namespace Trustbird\Evidence\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface HasEvidenceRelations
{
    public function workspace(): BelongsTo;

    public function evidence(): BelongsTo;

    public function related(): MorphTo;
}
