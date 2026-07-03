<?php

declare(strict_types=1);

namespace Trustbird\Policies\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trustbird\Policies\Models\Policy;

class PolicyCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Policy $policy,
    ) {}
}
