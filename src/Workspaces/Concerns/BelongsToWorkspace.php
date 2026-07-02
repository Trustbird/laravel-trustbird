<?php

declare(strict_types=1);

namespace Trustbird\Workspaces\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Trustbird\Workspaces\Models\Workspace;

trait BelongsToWorkspace
{
    public function initializeBelongsToWorkspace(): void
    {
        $this->mergeFillable(['workspace_id']);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}
