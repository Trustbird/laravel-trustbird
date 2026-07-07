<?php

declare(strict_types=1);

namespace Trustbird\Incidents\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasIncidents
{
    public function workspace(): BelongsTo;

    public function owner(): BelongsTo;

    public function responder(): BelongsTo;

    public function notes(): HasMany;

    public function isResolved(): bool;

    public function isArchived(): bool;

    public function isActive(): bool;
}

