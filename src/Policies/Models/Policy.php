<?php

declare(strict_types=1);

namespace Trustbird\Policies\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Trustbird\Database\Factories\Policy\PolicyFactory;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

final class Policy extends Model
{
    use BelongsToWorkspace;
    use HasFactory;
    use HasUlids;

    protected $table = 'policies';

    protected $fillable = [
        'title',
        'owner_id',
        'reviewer_id',
        'current_version_id',
        'reviewed_at',
        'next_review_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'next_review_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): PolicyFactory
    {
        return PolicyFactory::new();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'owner_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'reviewer_id');
    }

    public function publishedVersion(): BelongsTo
    {
        return $this->belongsTo(PolicyVersion::class, 'current_version_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PolicyVersion::class);
    }

    public function hasPublishedVersion(): bool
    {
        return $this->current_version_id !== null;
    }

    public function needsReview(): bool
    {
        if ($this->next_review_at === null) {
            return true;
        }

        return $this->next_review_at->isPast();
    }
}
