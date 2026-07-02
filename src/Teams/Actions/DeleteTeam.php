<?php

declare(strict_types=1);

namespace Trustbird\Teams\Actions;

use Trustbird\Teams\Events\TeamDeleted;
use Trustbird\Teams\Models\Team;

final readonly class DeleteTeam
{
    public function handle(Team $team): bool
    {
        $deleted = $team->delete();

        if ($deleted) {
            TeamDeleted::dispatch($team);
        }

        return $deleted;
    }
}
