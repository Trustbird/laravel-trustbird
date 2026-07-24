<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasInterviews
{
    public function workspace(): BelongsTo;

    public function owner(): BelongsTo;

    public function questions(): HasMany;

    public function answers(): HasMany;

    public function isCompleted(): bool;

    public function isInProgress(): bool;

    public function isArchived(): bool;

    public function progressPercent(): int;
}
