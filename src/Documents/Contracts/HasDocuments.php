<?php

declare(strict_types=1);

namespace Trustbird\Documents\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasDocuments
{
    public function workspace(): BelongsTo;

    public function owner(): BelongsTo;

    public function reviewer(): BelongsTo;

    public function currentVersion(): BelongsTo;

    public function versions(): HasMany;

    public function needsReview(): bool;
}
