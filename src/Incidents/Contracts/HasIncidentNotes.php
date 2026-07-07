<?php

declare(strict_types=1);

namespace Trustbird\Incidents\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasIncidentNotes
{
    public function workspace(): BelongsTo;

    public function incident(): BelongsTo;

    public function author(): BelongsTo;
}

