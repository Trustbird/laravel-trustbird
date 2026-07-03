<?php

declare(strict_types=1);

namespace Trustbird\Policies\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Trustbird\Database\Factories\Policy\PolicyVersionFactory;
use Trustbird\People\Models\Person;
use Trustbird\Policies\Enums\PolicyVersionStatus;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

final class PolicyVersion extends Model
{
    use BelongsToWorkspace;
    use HasFactory;
    use HasUlids;

    protected $table = 'policy_versions';

    protected $attributes = [
        'status' => 'draft',
    ];

    protected $fillable = [
        'policy_id',
        'version_number',
        'status',
        'content',
        'change_summary',
        'published_at',
        'published_by_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => PolicyVersionStatus::class,
            'published_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): PolicyVersionFactory
    {
        return PolicyVersionFactory::new();
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'published_by_id');
    }

    public function isDraft(): bool
    {
        return $this->status === PolicyVersionStatus::Draft;
    }

    public function isPublished(): bool
    {
        return $this->status === PolicyVersionStatus::Published;
    }

    public function isSuperseded(): bool
    {
        return $this->status === PolicyVersionStatus::Superseded;
    }

    public function canBeEdited(): bool
    {
        return $this->isDraft();
    }

    public function canBePublished(): bool
    {
        return $this->isDraft();
    }
}
