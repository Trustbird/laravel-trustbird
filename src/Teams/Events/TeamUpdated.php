<?php

declare(strict_types=1);

namespace Trustbird\Teams\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trustbird\Teams\Models\Team;

class TeamUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Team $team,
    ) {}
}
