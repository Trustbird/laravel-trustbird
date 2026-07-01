<?php

declare(strict_types=1);

namespace Trustbird\People\Actions;

use Trustbird\People\Events\PersonnelReminderRecorded;
use Trustbird\People\Models\Person;

final readonly class RecordPersonnelReminder
{
    public function handle(Person $person, array $reminderData = []): void
    {
        PersonnelReminderRecorded::dispatch($person, $reminderData);
    }
}