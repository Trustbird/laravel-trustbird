<?php

declare(strict_types=1);

namespace Trustbird\Teams\Actions;

use Trustbird\People\Models\Person;
use Trustbird\Teams\Events\MemberAddedToTeam;
use Trustbird\Teams\Models\Team;

final readonly class AddMemberToTeam
{
    /**
     * @param array<int, string|Person>|string|Person $people
     */
    public function handle(Team $team, array|string|Person $people): void
    {
        $personIds = collect(is_array($people) ? $people : [$people])
            ->map(fn (string|Person $person) => $person instanceof Person ? $person->id : $person)
            ->all();

        $team->members()->syncWithoutDetaching($personIds);

        MemberAddedToTeam::dispatch($team, $personIds);
    }
}
