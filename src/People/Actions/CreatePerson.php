<?php

declare(strict_types=1);

namespace Trustbird\People\Actions;

use Trustbird\People\Events\PersonCreated;
use Trustbird\People\Models\Person;

final readonly class CreatePerson
{
    public function handle(array $attributes): Person
    {
        $person = Person::query()->create($attributes);

        PersonCreated::dispatch($person);

        return $person;
    }
}