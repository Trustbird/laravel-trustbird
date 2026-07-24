<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Frameworks\Contracts\HasFrameworkMappings;
use Trustbird\Frameworks\Models\Concerns\InteractsWithFrameworkMappings;

final class FrameworkMapping extends Model implements HasFrameworkMappings
{
    use HasFactory, InteractsWithFrameworkMappings {
        InteractsWithFrameworkMappings::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'framework_mappings';

    protected $attributes = [
        'coverage' => 'planned',
    ];
}
