<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Trustbird\Database\Factories\Framework\FrameworkVersionFactory;
use Trustbird\Frameworks\Contracts\HasFrameworkRequirements;
use Trustbird\Frameworks\Contracts\HasFrameworks;
use Trustbird\Frameworks\Enums\FrameworkVersionStatus;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithFrameworkVersions
{
    use BelongsToWorkspace;

    public function initializeInteractsWithFrameworkVersions(): void
    {
        $this->mergeFillable([
            'framework_id',
            'version_label',
            'status',
            'change_summary',
            'published_at',
            'published_by_id',
            'metadata',
        ]);

        $this->mergeCasts([
            'status' => FrameworkVersionStatus::class,
            'published_at' => 'datetime',
            'metadata' => 'array',
        ]);
    }

    public function framework(): BelongsTo
    {
        return $this->belongsTo(app(HasFrameworks::class)::class);
    }

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(app(HasPeople::class)::class, 'published_by_id');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(app(HasFrameworkRequirements::class)::class);
    }

    public function isDraft(): bool
    {
        return $this->status === FrameworkVersionStatus::Draft;
    }

    public function isPublished(): bool
    {
        return $this->status === FrameworkVersionStatus::Published;
    }

    public function isSuperseded(): bool
    {
        return $this->status === FrameworkVersionStatus::Superseded;
    }

    public function canBePublished(): bool
    {
        return $this->isDraft();
    }

    protected static function newFactory(): FrameworkVersionFactory
    {
        return FrameworkVersionFactory::new();
    }
}
