<?php

declare(strict_types=1);

namespace Trustbird\Workspaces\Managers;

use Trustbird\Workspaces\Contracts\HasWorkspaces;

final readonly class WorkspacesManager
{
    public function create(
        string $name,
        ?string $slug = null,
        ?string $description = null,
        array $metadata = [],
    ): HasWorkspaces {
        /** @var HasWorkspaces $model */
        $model = app(HasWorkspaces::class);

        return $model->query()->create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    public function update(
        HasWorkspaces $workspace,
        ?string $name = null,
        ?string $slug = null,
        ?string $description = null,
        ?array $metadata = null,
    ): HasWorkspaces {
        $attributes = array_filter([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $workspace->update($attributes);

        return $workspace;
    }

    public function archive(HasWorkspaces $workspace): HasWorkspaces
    {
        // For now, we'll just mark it as archived in metadata if no dedicated action exists.
        $metadata = $workspace->metadata ?? [];
        $metadata['archived_at'] = now()->toIso8601String();

        return $this->update($workspace, metadata: $metadata);
    }
}
