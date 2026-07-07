<?php

declare(strict_types=1);

namespace Trustbird\Tasks\Managers;

use DateTimeInterface;
use Trustbird\Tasks\Contracts\HasTaskLinks;
use Trustbird\Tasks\Contracts\HasTasks;
use Trustbird\Tasks\Enums\TaskPriority;
use Trustbird\Tasks\Enums\TaskStatus;
use Trustbird\Tasks\Models\Task;

final readonly class TasksManager
{
    public function create(
        string $title,
        ?string $description = null,
        TaskPriority $priority = TaskPriority::Normal,
        TaskStatus $status = TaskStatus::Open,
        ?string $ownerId = null,
        ?string $assigneeId = null,
        ?DateTimeInterface $dueAt = null,
        array $metadata = [],
        ?string $workspaceId = null,
    ): HasTasks {
        /** @var HasTasks $model */
        $model = app(HasTasks::class);

        return $model->query()->create([
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'status' => $status,
            'owner_id' => $ownerId,
            'assignee_id' => $assigneeId,
            'due_at' => $dueAt,
            'metadata' => $metadata,
            'workspace_id' => $workspaceId,
        ]);
    }

    public function update(
        HasTasks $task,
        ?string $title = null,
        ?string $description = null,
        ?TaskPriority $priority = null,
        ?TaskStatus $status = null,
        ?string $ownerId = null,
        ?string $assigneeId = null,
        ?DateTimeInterface $dueAt = null,
        ?DateTimeInterface $completedAt = null,
        ?array $metadata = null,
    ): HasTasks {
        $attributes = array_filter([
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'status' => $status,
            'owner_id' => $ownerId,
            'assignee_id' => $assigneeId,
            'due_at' => $dueAt,
            'completed_at' => $completedAt,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $task->update($attributes);

        return $task;
    }

    public function assign(HasTasks $task, ?string $assigneeId = null): HasTasks
    {
        return $this->update($task, assigneeId: $assigneeId);
    }

    public function complete(HasTasks $task, ?DateTimeInterface $completedAt = null): HasTasks
    {
        return $this->update($task,
            status: TaskStatus::Completed,
            completedAt: $completedAt ?? now(),
        );
    }

    public function link(
        Task $task,
        object $related,
        array $metadata = [],
    ): HasTaskLinks {
        /** @var HasTaskLinks $model */
        $model = app(HasTaskLinks::class);

        return $model->query()->create([
            'workspace_id' => $task->workspace_id,
            'task_id' => $task->id,
            'related_type' => $related::class,
            'related_id' => $related->id,
            'metadata' => $metadata,
        ]);
    }
}

