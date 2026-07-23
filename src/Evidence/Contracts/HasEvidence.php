<?php

declare(strict_types=1);

namespace Trustbird\Evidence\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasEvidence
{
    public function workspace(): BelongsTo;

    public function owner(): BelongsTo;

    public function reviewer(): BelongsTo;

    public function relations(): HasMany;

    public function isReviewOverdue(): bool;
}
