<?php

declare(strict_types=1);

namespace Trustbird\Reviews\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasReviewReviewers
{
    public function workspace(): BelongsTo;

    public function review(): BelongsTo;

    public function person(): BelongsTo;
}
