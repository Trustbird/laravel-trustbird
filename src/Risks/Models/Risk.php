<?php

declare(strict_types=1);

namespace Trustbird\Risks\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Risks\Contracts\HasRisks;
use Trustbird\Risks\Models\Concerns\InteractsWithRisks;

final class Risk extends Model implements HasRisks
{
    use HasFactory, InteractsWithRisks {
        InteractsWithRisks::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'risks';
}
