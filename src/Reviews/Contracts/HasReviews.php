<?php

declare(strict_types=1);

namespace Trustbird\Reviews\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface HasReviews
{
    public function workspace(): BelongsTo;

    public function reviewable(): MorphTo;

    public function reviewer(): BelongsTo;

    public function reviewers(): HasMany;

    public function isDue(): bool;

    public function isCompleted(): bool;
}
