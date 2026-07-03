<?php

declare(strict_types=1);

namespace Trustbird\Teams\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Trustbird\Database\Factories\Team\TeamFactory;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithTeams
{
    use BelongsToWorkspace;

    public function initializeInteractsWithTeams(): void
    {
        $this->mergeFillable([
            'name',
            'description',
            'owner_id',
        ]);
    }

    protected static function newFactory(): TeamFactory
    {
        return TeamFactory::new();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(app(HasPeople::class)::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(app(HasPeople::class)::class, 'team_person', 'team_id', 'person_id');
    }
}
