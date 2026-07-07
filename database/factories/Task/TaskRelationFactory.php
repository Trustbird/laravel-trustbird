<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Task;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Trustbird\Risks\Models\Risk;
use Trustbird\Tasks\Models\Task;
use Trustbird\Tasks\Models\TaskRelation;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<TaskRelation>
 */
final class TaskRelationFactory extends Factory
{
    protected $model = TaskRelation::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'task_id' => Task::factory(),
            'related_type' => Risk::class,
            'related_id' => (string) Str::ulid(),
            'metadata' => [],
        ];
    }
}

