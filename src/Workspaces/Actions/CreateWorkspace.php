<?php

declare(strict_types=1);

namespace Trustbird\Workspaces\Actions;

use Trustbird\Workspaces\Events\WorkspaceCreated;
use Trustbird\Workspaces\Models\Workspace;

final readonly class CreateWorkspace
{
    /**
     * @param array{
     *     name: string,
     *     slug: string,
     *     description?: string|null,
     *     metadata?: array|null,
     * } $attributes
     */
    public function handle(array $attributes): Workspace
    {
        $workspace = Workspace::query()->create($attributes);

        WorkspaceCreated::dispatch($workspace);

        return $workspace;
    }
}
