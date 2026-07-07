<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Database\Factories\Task\TaskFactory;
use Trustbird\Database\Factories\Task\TaskRelationFactory;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Models\Person;
use Trustbird\Risks\Models\Risk;
use Trustbird\Tasks\Enums\TaskPriority;
use Trustbird\Tasks\Enums\TaskStatus;
use Trustbird\Tasks\Models\Task;
use Trustbird\Tasks\Models\TaskRelation;

beforeEach(fn () => Event::fake());

test('it can create a task, assign it, and dispatches eloquent event', function (): void {
    $owner = Person::factory()->create();
    $assignee = Person::factory()->create(['workspace_id' => $owner->workspace_id]);

    $task = Trustbird::tasks()->create(
        title: 'Review access control policy',
        description: 'Ensure the policy matches current practices.',
        priority: TaskPriority::High,
        ownerId: $owner->id,
        assigneeId: $assignee->id,
    );

    expect($task)->toBeInstanceOf(Task::class)
        ->and($task->title)->toBe('Review access control policy')
        ->and($task->priority)->toBe(TaskPriority::High)
        ->and($task->status)->toBe(TaskStatus::Open)
        ->and($task->owner_id)->toBe($owner->id)
        ->and($task->assignee_id)->toBe($assignee->id);

    expect($task->owner)->toBeInstanceOf(Person::class);
    expect($task->assignee)->toBeInstanceOf(Person::class);

    Event::assertDispatched('eloquent.created: '.Task::class);
});

test('it can update a task and dispatches eloquent event', function (): void {
    /** @var Task $task */
    $task = Task::factory()->create([
        'title' => 'Old',
        'priority' => TaskPriority::Normal,
        'status' => TaskStatus::Open,
    ]);

    $updated = Trustbird::tasks()->update($task,
        title: 'New',
        priority: TaskPriority::Urgent,
        status: TaskStatus::InProgress,
    );

    expect($updated->title)->toBe('New')
        ->and($updated->priority)->toBe(TaskPriority::Urgent)
        ->and($updated->status)->toBe(TaskStatus::InProgress);

    Event::assertDispatched('eloquent.updated: '.Task::class);
});

test('it can complete a task and dispatches eloquent event', function (): void {
    /** @var Task $task */
    $task = Task::factory()->create(['status' => TaskStatus::Open]);

    $completed = Trustbird::tasks()->complete($task);

    expect($completed->status)->toBe(TaskStatus::Completed)
        ->and($completed->completed_at)->not->toBeNull()
        ->and($completed->isCompleted())->toBeTrue();

    Event::assertDispatched('eloquent.updated: '.Task::class);
});

test('it can assign a task via manager helper', function (): void {
    $owner = Person::factory()->create();
    $assignee = Person::factory()->create(['workspace_id' => $owner->workspace_id]);

    /** @var Task $task */
    $task = Task::factory()->create([
        'workspace_id' => $owner->workspace_id,
        'owner_id' => $owner->id,
        'assignee_id' => null,
    ]);

    $assigned = Trustbird::tasks()->assign($task, assigneeId: $assignee->id);

    expect($assigned->assignee_id)->toBe($assignee->id);
});

test('it can link a task to a canonical Trustbird object and dispatches eloquent event', function (): void {
    $task = Task::factory()->create();
    $risk = Risk::factory()->create(['workspace_id' => $task->workspace_id]);

    $link = Trustbird::tasks()->link(
        task: $task,
        related: $risk,
        metadata: ['reason' => 'created from risk review'],
    );

    expect($link)->toBeInstanceOf(TaskRelation::class)
        ->and($link->task_id)->toBe($task->id)
        ->and($link->related_type)->toBe(Risk::class)
        ->and($link->related_id)->toBe($risk->id);

    expect($link->task)->toBeInstanceOf(Task::class);
    expect($link->related)->toBeInstanceOf(Risk::class);

    Event::assertDispatched('eloquent.created: '.TaskRelation::class);
});

test('it covers task and link model methods and factories', function (): void {
    $owner = Person::factory()->create();
    $assignee = Person::factory()->create(['workspace_id' => $owner->workspace_id]);
    $risk = Risk::factory()->create(['workspace_id' => $owner->workspace_id]);

    $task = Task::factory()->create([
        'workspace_id' => $owner->workspace_id,
        'owner_id' => $owner->id,
        'assignee_id' => $assignee->id,
        'metadata' => ['source' => 'governance'],
        'status' => TaskStatus::Completed,
        'completed_at' => now(),
    ]);

    $link = TaskRelation::factory()->create([
        'workspace_id' => $task->workspace_id,
        'task_id' => $task->id,
        'related_type' => $risk::class,
        'related_id' => $risk->id,
        'metadata' => ['confidence' => 'high'],
    ]);

    expect($task->isCompleted())->toBeTrue();
    expect($task->links)->toHaveCount(1)->and($task->links->first()->id)->toBe($link->id);

    expect($task->metadata)->toBeArray()->and($task->metadata['source'])->toBe('governance');
    expect($link->metadata)->toBeArray()->and($link->metadata['confidence'])->toBe('high');

    expect(Task::newFactory())->toBeInstanceOf(TaskFactory::class);
    expect(TaskRelation::newFactory())->toBeInstanceOf(TaskRelationFactory::class);
});

