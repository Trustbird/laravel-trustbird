<?php

declare(strict_types=1);

namespace Trustbird\People\Actions;

use Trustbird\People\Models\Person;

final readonly class UpdatePerson
{
    public function handle(Person $person, array $attributes): Person
    {
        $person->update($attributes);

        return $person->refresh();
    }
}