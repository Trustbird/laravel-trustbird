<?php

declare(strict_types=1);

namespace Trustbird\People\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Trustbird\Database\Factories\Person\PersonFactory;
use Trustbird\People\Enums\EmploymentStatus;
use Trustbird\People\Enums\EmploymentType;
use Trustbird\Teams\Models\Team;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;
use Trustbird\Workspaces\Models\Workspace;

final class Person extends Model
{
    use BelongsToWorkspace;
    use HasFactory;
    use HasUlids;

    protected $table = 'people';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'employment_type',
        'employment_status',
        'started_at',
        'ended_at',
        'last_reminded_at',
        'metadata',
    ];

    protected static function newFactory(): PersonFactory
    {
        return PersonFactory::new();
    }

    protected function casts(): array
    {
        return [
            'employment_type' => EmploymentType::class,
            'employment_status' => EmploymentStatus::class,
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'last_reminded_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function ownedTeams(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function teams(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_person', 'person_id', 'team_id');
    }
}