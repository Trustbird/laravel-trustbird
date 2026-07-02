<?php

declare(strict_types=1);

namespace Trustbird\Workspaces\Actions;

use Trustbird\Workspaces\Events\WorkspaceUpdated;
use Trustbird\Workspaces\Models\Workspace;

final readonly class UpdateWorkspace
{
    /**
     * @param array{
     *     name?: string,
     *     slug?: string,
     *     description?: string|null,
     *     metadata?: array|null,
     * } $attributes
     */
    public function handle(Workspace $workspace, array $attributes): Workspace
    {
        $workspace->update($attributes);

        WorkspaceUpdated::dispatch($workspace);

        return $workspace;
    }
}
