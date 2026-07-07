<?php

declare(strict_types=1);

namespace Trustbird\Tasks\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Trustbird\Database\Factories\Task\TaskRelationFactory;
use Trustbird\Tasks\Contracts\HasTaskLinks;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

final class TaskRelation extends Model implements HasTaskLinks
{
    use BelongsToWorkspace;
    use HasFactory;
    use HasUlids;

    protected $table = 'task_relations';

    protected $fillable = [
        'task_id',
        'related_type',
        'related_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): TaskRelationFactory
    {
        return TaskRelationFactory::new();
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}

