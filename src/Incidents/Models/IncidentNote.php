<?php

declare(strict_types=1);

namespace Trustbird\Incidents\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Trustbird\Database\Factories\Incident\IncidentNoteFactory;
use Trustbird\Incidents\Contracts\HasIncidentNotes;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

final class IncidentNote extends Model implements HasIncidentNotes
{
    use BelongsToWorkspace;
    use HasFactory;
    use HasUlids;

    protected $table = 'incident_notes';

    protected $fillable = [
        'incident_id',
        'author_id',
        'occurred_at',
        'body',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): IncidentNoteFactory
    {
        return IncidentNoteFactory::new();
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'author_id');
    }
}

