<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Facades\Trustbird;
use Trustbird\Tasks\Enums\TaskPriority;
use Trustbird\Tasks\Enums\TaskStatus;
use Trustbird\Tasks\Models\Task;

beforeEach(fn () => Event::fake());

it('can create a task via the facade', function () {
    $task = Trustbird::tasks()->create(
        title: 'Create evidence request',
        priority: TaskPriority::Normal,
    );

    expect($task)->toBeInstanceOf(Task::class)
        ->and($task->title)->toBe('Create evidence request')
        ->and($task->priority)->toBe(TaskPriority::Normal)
        ->and($task->status)->toBe(TaskStatus::Open);

    Event::assertDispatched('eloquent.created: '.Task::class);
});

it('can complete a task via the facade', function () {
    $task = Task::factory()->create(['status' => TaskStatus::Open]);

    $completed = Trustbird::tasks()->complete($task);

    expect($completed->status)->toBe(TaskStatus::Completed)
        ->and($completed->completed_at)->not->toBeNull();

    Event::assertDispatched('eloquent.updated: '.Task::class);
});

