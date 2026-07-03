<?php

declare(strict_types=1);

namespace Trustbird\Teams\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Teams\Contracts\HasTeams;
use Trustbird\Teams\Models\Concerns\InteractsWithTeams;

final class Team extends Model implements HasTeams
{
    use HasFactory, InteractsWithTeams {
        InteractsWithTeams::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'teams';
}
