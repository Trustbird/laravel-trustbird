<?php

declare(strict_types=1);

namespace Trustbird\People\Actions;

use Trustbird\People\Events\PersonUpdated;
use Trustbird\People\Models\Person;

final readonly class UpdatePerson
{
    public function handle(Person $person, array $attributes): Person
    {
        $person->update($attributes);

        PersonUpdated::dispatch($person);

        return $person->refresh();
    }
}