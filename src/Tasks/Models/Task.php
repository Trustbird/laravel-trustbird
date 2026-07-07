<?php

declare(strict_types=1);

namespace Trustbird\Tasks\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Trustbird\Database\Factories\Task\TaskFactory;
use Trustbird\People\Models\Person;
use Trustbird\Tasks\Contracts\HasTasks;
use Trustbird\Tasks\Enums\TaskPriority;
use Trustbird\Tasks\Enums\TaskStatus;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

final class Task extends Model implements HasTasks
{
    use BelongsToWorkspace;
    use HasFactory;
    use HasUlids;

    protected $table = 'tasks';

    protected $attributes = [
        'status' => 'open',
        'priority' => 'normal',
    ];

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'owner_id',
        'assignee_id',
        'due_at',
        'completed_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): TaskFactory
    {
        return TaskFactory::new();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'owner_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'assignee_id');
    }

    public function links(): HasMany
    {
        return $this->hasMany(TaskRelation::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === TaskStatus::Completed;
    }
}

