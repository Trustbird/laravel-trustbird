<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasFrameworkVersions
{
    public function workspace(): BelongsTo;

    public function framework(): BelongsTo;

    public function requirements(): HasMany;

    public function isDraft(): bool;

    public function isPublished(): bool;

    public function canBePublished(): bool;
}
