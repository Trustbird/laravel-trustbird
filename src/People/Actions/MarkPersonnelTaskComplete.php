<?php

declare(strict_types=1);

namespace Trustbird\People\Actions;

use Trustbird\People\Events\PersonnelTaskMarkedComplete;
use Trustbird\People\Models\Person;

final readonly class MarkPersonnelTaskComplete
{
    public function handle(Person $person, array $taskData = []): void
    {
        PersonnelTaskMarkedComplete::dispatch($person, $taskData);
    }
}
