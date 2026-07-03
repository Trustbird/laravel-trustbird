<?php

declare(strict_types=1);

namespace Trustbird\Risks\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasRisks
{
    public function owner(): BelongsTo;

    public function workspace(): BelongsTo;

    public function isResolved(): bool;

    public function isArchived(): bool;

    public function isActive(): bool;

    public function needsReview(): bool;
}
