<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Frameworks\Contracts\HasFrameworks;
use Trustbird\Frameworks\Models\Concerns\InteractsWithFrameworks;

final class Framework extends Model implements HasFrameworks
{
    use HasFactory, InteractsWithFrameworks {
        InteractsWithFrameworks::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'frameworks';
}
