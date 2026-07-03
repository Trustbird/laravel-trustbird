<?php

declare(strict_types=1);

namespace Trustbird\People\Actions;

use Trustbird\People\Contracts\HasPeople;
use Trustbird\People\Events\PersonnelReminderRecorded;

final readonly class RecordPersonnelReminder
{
    public function handle(HasPeople $person, array $reminderData = []): void
    {
        PersonnelReminderRecorded::dispatch($person, $reminderData);
    }
}
