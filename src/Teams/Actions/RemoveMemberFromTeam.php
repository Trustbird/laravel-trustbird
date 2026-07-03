<?php

declare(strict_types=1);

namespace Trustbird\Teams\Actions;

use Trustbird\People\Contracts\HasPeople;
use Trustbird\Teams\Contracts\HasTeams;
use Trustbird\Teams\Events\MemberRemovedFromTeam;

final readonly class RemoveMemberFromTeam
{
    /**
     * @param  array<int, string|HasPeople>|string|HasPeople  $people
     */
    public function handle(HasTeams $team, array|string|HasPeople $people): void
    {
        $personIds = collect(is_array($people) ? $people : [$people])
            ->map(fn (string|HasPeople $person) => $person instanceof HasPeople ? $person->id : $person)
            ->all();

        $team->members()->detach($personIds);

        MemberRemovedFromTeam::dispatch($team, $personIds);
    }
}
