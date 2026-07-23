<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasInterviewAnswers
{
    public function workspace(): BelongsTo;

    public function interview(): BelongsTo;

    public function question(): BelongsTo;

    public function answeredBy(): BelongsTo;
}
