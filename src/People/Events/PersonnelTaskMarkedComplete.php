<?php

declare(strict_types=1);

namespace Trustbird\People\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trustbird\People\Contracts\HasPeople;

class PersonnelTaskMarkedComplete
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public HasPeople $person,
        public array $taskData = [],
    ) {}
}
