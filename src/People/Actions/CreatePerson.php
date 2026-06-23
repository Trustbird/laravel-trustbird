<?php

declare(strict_types=1);

namespace Trustbird\People\Actions;

use Trustbird\People\Models\Person;

final readonly class CreatePerson
{
    public function handle(array $attributes): Person
    {
        return Person::query()->create($attributes);
    }
}