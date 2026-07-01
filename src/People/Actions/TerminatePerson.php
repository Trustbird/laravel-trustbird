<?php

declare(strict_types=1);

namespace Trustbird\People\Actions;

use Trustbird\People\Enums\EmploymentStatus;
use Trustbird\People\Events\PersonTerminated;
use Trustbird\People\Models\Person;

final readonly class TerminatePerson
{
    public function handle(Person $person): Person
    {
        $person->update([
            'employment_status' => EmploymentStatus::Terminated,
            'ended_at' => now(),
        ]);

        PersonTerminated::dispatch($person);

        return $person->refresh();
    }
}