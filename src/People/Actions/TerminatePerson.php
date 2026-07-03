<?php

declare(strict_types=1);

namespace Trustbird\People\Actions;

use Trustbird\People\Contracts\HasPeople;
use Trustbird\People\Enums\EmploymentStatus;
use Trustbird\People\Events\PersonTerminated;

final readonly class TerminatePerson
{
    public function handle(HasPeople $person): HasPeople
    {
        $person->update([
            'employment_status' => EmploymentStatus::Terminated,
            'ended_at' => now(),
        ]);

        PersonTerminated::dispatch($person);

        return $person->refresh();
    }
}
