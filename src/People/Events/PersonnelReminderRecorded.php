<?php

declare(strict_types=1);

namespace Trustbird\People\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trustbird\People\Models\Person;

class PersonnelReminderRecorded
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Person $person,
        public array $reminderData = [],
    ) {}
}
