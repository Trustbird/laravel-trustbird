<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

interface HasInterviewQuestions
{
    public function workspace(): BelongsTo;

    public function interview(): BelongsTo;

    public function answer(): HasOne;
}
