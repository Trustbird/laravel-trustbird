<?php

declare(strict_types=1);

namespace Trustbird\Teams\Actions;

use Trustbird\Teams\Events\TeamUpdated;
use Trustbird\Teams\Models\Team;

final readonly class UpdateTeam
{
    public function handle(Team $team, array $attributes): Team
    {
        $team->update($attributes);

        TeamUpdated::dispatch($team);

        return $team->refresh();
    }
}
