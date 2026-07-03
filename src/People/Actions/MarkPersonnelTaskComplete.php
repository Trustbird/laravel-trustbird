<?php

declare(strict_types=1);

namespace Trustbird\People\Actions;

use Trustbird\People\Contracts\HasPeople;
use Trustbird\People\Events\PersonnelTaskMarkedComplete;

final readonly class MarkPersonnelTaskComplete
{
    public function handle(HasPeople $person, array $taskData = []): void
    {
        PersonnelTaskMarkedComplete::dispatch($person, $taskData);
    }
}
