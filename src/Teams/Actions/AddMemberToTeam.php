<?php

declare(strict_types=1);

namespace Trustbird\Teams\Actions;

use Trustbird\People\Contracts\HasPeople;
use Trustbird\Teams\Contracts\HasTeams;
use Trustbird\Teams\Events\MemberAddedToTeam;

final readonly class AddMemberToTeam
{
    /**
     * @param  array<int, string|HasPeople>|string|HasPeople  $people
     */
    public function handle(HasTeams $team, array|string|HasPeople $people): void
    {
        $personIds = collect(is_array($people) ? $people : [$people])
            ->map(fn (string|HasPeople $person) => $person instanceof HasPeople ? $person->id : $person)
            ->all();

        $team->members()->syncWithoutDetaching($personIds);

        MemberAddedToTeam::dispatch($team, $personIds);
    }
}
