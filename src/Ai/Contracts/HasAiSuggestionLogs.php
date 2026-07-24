<?php

declare(strict_types=1);

namespace Trustbird\Ai\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasAiSuggestionLogs
{
    public function workspace(): BelongsTo;

    public function suggestion(): BelongsTo;

    public function actor(): BelongsTo;
}
