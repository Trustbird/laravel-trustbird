<?php

declare(strict_types=1);

namespace Trustbird\People\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasPeople
{
    public function ownedTeams(): HasMany;

    public function teams(): BelongsToMany;

    public function workspace(): BelongsTo;
}
