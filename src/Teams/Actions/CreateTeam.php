<?php

declare(strict_types=1);

namespace Trustbird\Teams\Actions;

use Trustbird\Teams\Events\TeamCreated;
use Trustbird\Teams\Models\Team;

final readonly class CreateTeam
{
    public function handle(array $attributes): Team
    {
        $team = Team::query()->create($attributes);

        TeamCreated::dispatch($team);

        return $team;
    }
}
