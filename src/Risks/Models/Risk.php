<?php

declare(strict_types=1);

namespace Trustbird\Risks\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Trustbird\Database\Factories\Risk\RiskFactory;
use Trustbird\People\Models\Person;
use Trustbird\Risks\Enums\RiskLevel;
use Trustbird\Risks\Enums\RiskStatus;
use Trustbird\Risks\Enums\RiskTreatment;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

final class Risk extends Model
{
    use BelongsToWorkspace;
    use HasFactory;
    use HasUlids;

    protected $table = 'risks';

    protected $attributes = [
        'status' => 'open',
    ];

    protected $fillable = [
        'title',
        'description',
        'owner_id',
        'status',
        'treatment',
        'likelihood',
        'impact',
        'reviewed_at',
        'next_review_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => RiskStatus::class,
            'treatment' => RiskTreatment::class,
            'likelihood' => RiskLevel::class,
            'impact' => RiskLevel::class,
            'reviewed_at' => 'datetime',
            'next_review_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): RiskFactory
    {
        return RiskFactory::new();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'owner_id');
    }

    public function isResolved(): bool
    {
        return $this->status === RiskStatus::Resolved;
    }

    public function isArchived(): bool
    {
        return $this->status === RiskStatus::Archived;
    }

    public function isActive(): bool
    {
        return ! $this->isResolved() && ! $this->isArchived();
    }

    public function needsReview(): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        if ($this->next_review_at === null) {
            return true;
        }

        return $this->next_review_at->isPast();
    }
}
