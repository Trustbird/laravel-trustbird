<?php

declare(strict_types=1);

namespace Trustbird\Teams\Managers;

use Trustbird\People\Contracts\HasPeople;
use Trustbird\Teams\Actions\AddMemberToTeam;
use Trustbird\Teams\Actions\RemoveMemberFromTeam;
use Trustbird\Teams\Contracts\HasTeams;

final readonly class TeamsManager
{
    public function create(
        string $name,
        ?string $description = null,
        ?string $ownerId = null,
        ?string $workspaceId = null,
    ): HasTeams {
        /** @var HasTeams $model */
        $model = app(HasTeams::class);

        return $model->query()->create([
            'name' => $name,
            'description' => $description,
            'owner_id' => $ownerId,
            'workspace_id' => $workspaceId,
        ]);
    }

    public function update(
        HasTeams $team,
        ?string $name = null,
        ?string $description = null,
        ?string $ownerId = null,
    ): HasTeams {
        $attributes = array_filter([
            'name' => $name,
            'description' => $description,
            'owner_id' => $ownerId,
        ], fn ($value) => $value !== null);

        $team->update($attributes);

        return $team;
    }

    public function delete(HasTeams $team): bool
    {
        return $team->delete();
    }

    public function addMember(HasTeams $team, HasPeople|array|string $person): void
    {
        app(AddMemberToTeam::class)->handle($team, $person);
    }

    public function removeMember(HasTeams $team, HasPeople|array|string $person): void
    {
        app(RemoveMemberFromTeam::class)->handle($team, $person);
    }
}
