<?php

declare(strict_types=1);

namespace Trustbird\Incidents\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Trustbird\Database\Factories\Incident\IncidentFactory;
use Trustbird\Incidents\Contracts\HasIncidents;
use Trustbird\Incidents\Enums\IncidentSeverity;
use Trustbird\Incidents\Enums\IncidentStatus;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

final class Incident extends Model implements HasIncidents
{
    use BelongsToWorkspace;
    use HasFactory;
    use HasUlids;

    protected $table = 'incidents';

    protected $attributes = [
        'status' => 'open',
        'severity' => 'medium',
    ];

    protected $fillable = [
        'title',
        'description',
        'severity',
        'status',
        'owner_id',
        'responder_id',
        'detected_at',
        'contained_at',
        'resolved_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'severity' => IncidentSeverity::class,
            'status' => IncidentStatus::class,
            'detected_at' => 'datetime',
            'contained_at' => 'datetime',
            'resolved_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): IncidentFactory
    {
        return IncidentFactory::new();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'owner_id');
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'responder_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(IncidentNote::class);
    }

    public function isResolved(): bool
    {
        return $this->status === IncidentStatus::Resolved;
    }

    public function isArchived(): bool
    {
        return $this->status === IncidentStatus::Archived;
    }

    public function isActive(): bool
    {
        return ! $this->isResolved() && ! $this->isArchived();
    }
}

