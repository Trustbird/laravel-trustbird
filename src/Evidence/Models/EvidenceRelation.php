<?php

declare(strict_types=1);

namespace Trustbird\Evidence\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Evidence\Contracts\HasEvidenceRelations;
use Trustbird\Evidence\Models\Concerns\InteractsWithEvidenceRelations;

final class EvidenceRelation extends Model implements HasEvidenceRelations
{
    use HasFactory, InteractsWithEvidenceRelations {
        InteractsWithEvidenceRelations::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'evidence_relations';
}
