<?php

declare(strict_types=1);

namespace Trustbird\Workspaces\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Workspaces\Contracts\HasWorkspaces;
use Trustbird\Workspaces\Models\Concerns\InteractsWithWorkspaces;

final class Workspace extends Model implements HasWorkspaces
{
    use HasFactory, InteractsWithWorkspaces {
        InteractsWithWorkspaces::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'workspaces';
}
