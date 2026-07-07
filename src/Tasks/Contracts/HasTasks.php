<?php

declare(strict_types=1);

namespace Trustbird\Tasks\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasTasks
{
    public function workspace(): BelongsTo;

    public function owner(): BelongsTo;

    public function assignee(): BelongsTo;

    public function links(): HasMany;

    public function isCompleted(): bool;
}

