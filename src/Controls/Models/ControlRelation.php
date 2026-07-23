<?php

declare(strict_types=1);

namespace Trustbird\Controls\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Controls\Contracts\HasControlRelations;
use Trustbird\Controls\Models\Concerns\InteractsWithControlRelations;

final class ControlRelation extends Model implements HasControlRelations
{
    use HasFactory, InteractsWithControlRelations {
        InteractsWithControlRelations::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'control_relations';
}
