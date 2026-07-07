<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Task;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\People\Models\Person;
use Trustbird\Tasks\Enums\TaskPriority;
use Trustbird\Tasks\Enums\TaskStatus;
use Trustbird\Tasks\Models\Task;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<Task>
 */
final class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'status' => TaskStatus::Open,
            'priority' => $this->faker->randomElement(TaskPriority::cases()),
            'owner_id' => Person::factory(),
            'assignee_id' => Person::factory(),
            'due_at' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'metadata' => [],
        ];
    }

    public function open(): self
    {
        return $this->state(fn () => ['status' => TaskStatus::Open]);
    }

    public function completed(): self
    {
        return $this->state(fn () => [
            'status' => TaskStatus::Completed,
            'completed_at' => now(),
        ]);
    }
}

