<?php

declare(strict_types=1);

namespace Trustbird\Teams\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Trustbird\Database\Factories\Team\TeamFactory;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

final class Team extends Model
{
    use BelongsToWorkspace;
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'name',
        'description',
        'owner_id',
    ];

    protected static function newFactory(): TeamFactory
    {
        return TeamFactory::new();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'team_person', 'team_id', 'person_id');
    }
}
