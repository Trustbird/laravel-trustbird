<?php

declare(strict_types=1);

namespace Trustbird\Workspaces\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trustbird\Workspaces\Models\Workspace;

class WorkspaceUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Workspace $workspace,
    ) {
    }
}
