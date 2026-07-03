<?php

declare(strict_types=1);

namespace Trustbird\Risks\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trustbird\Risks\Contracts\HasRisks;

class RiskReviewed
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public HasRisks $risk,
    ) {}
}
