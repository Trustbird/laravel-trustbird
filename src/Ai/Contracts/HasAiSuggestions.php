<?php

declare(strict_types=1);

namespace Trustbird\Ai\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface HasAiSuggestions
{
    public function workspace(): BelongsTo;

    public function provider(): BelongsTo;

    public function prompt(): BelongsTo;

    public function subject(): MorphTo;

    public function createdBy(): BelongsTo;

    public function reviewedBy(): BelongsTo;

    public function logs(): HasMany;

    public function isPending(): bool;

    public function isApproved(): bool;

    public function isRejected(): bool;

    public function isWithdrawn(): bool;
}
