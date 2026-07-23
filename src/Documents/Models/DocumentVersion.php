<?php

declare(strict_types=1);

namespace Trustbird\Documents\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Trustbird\Database\Factories\Document\DocumentVersionFactory;
use Trustbird\Documents\Enums\DocumentVersionStatus;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

final class DocumentVersion extends Model
{
    use BelongsToWorkspace;
    use HasFactory;
    use HasUlids;

    protected $table = 'document_versions';

    protected $attributes = [
        'status' => 'draft',
    ];

    protected $fillable = [
        'document_id',
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
            'status' => DocumentVersionStatus::class,
            'published_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): DocumentVersionFactory
    {
        return DocumentVersionFactory::new();
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(app(HasPeople::class)::class, 'published_by_id');
    }

    public function isDraft(): bool
    {
        return $this->status === DocumentVersionStatus::Draft;
    }

    public function isPublished(): bool
    {
        return $this->status === DocumentVersionStatus::Published;
    }

    public function isSuperseded(): bool
    {
        return $this->status === DocumentVersionStatus::Superseded;
    }

    public function canBePublished(): bool
    {
        return $this->isDraft();
    }
}
