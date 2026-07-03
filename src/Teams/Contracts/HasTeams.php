<?php

declare(strict_types=1);

namespace Trustbird\Teams\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface HasTeams
{
    public function owner(): BelongsTo;

    public function members(): BelongsToMany;

    public function workspace(): BelongsTo;
}
