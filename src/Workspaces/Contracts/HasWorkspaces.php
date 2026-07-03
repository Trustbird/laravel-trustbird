<?php

declare(strict_types=1);

namespace Trustbird\Workspaces\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasWorkspaces
{
    public function people(): HasMany;

    public function assets(): HasMany;
}
