<?php

declare(strict_types=1);

namespace Trustbird\People\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Trustbird\Database\Factories\Person\PersonFactory;
use Trustbird\People\Enums\EmploymentStatus;
use Trustbird\People\Enums\EmploymentType;
use Trustbird\Teams\Contracts\HasTeams;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithPeople
{
    use BelongsToWorkspace;

    public function initializeInteractsWithPeople(): void
    {
        $this->mergeFillable([
            'first_name',
            'last_name',
            'email',
            'employment_type',
            'employment_status',
            'started_at',
            'ended_at',
            'last_reminded_at',
            'metadata',
        ]);

        $this->mergeCasts([
            'employment_type' => EmploymentType::class,
            'employment_status' => EmploymentStatus::class,
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'last_reminded_at' => 'datetime',
            'metadata' => 'array',
        ]);
    }

    protected static function newFactory(): PersonFactory
    {
        return PersonFactory::new();
    }

    public function ownedTeams(): HasMany
    {
        return $this->hasMany(app(HasTeams::class)::class, 'owner_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(app(HasTeams::class)::class, 'team_person', 'person_id', 'team_id');
    }
}
