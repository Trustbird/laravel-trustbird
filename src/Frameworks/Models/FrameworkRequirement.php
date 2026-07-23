<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Frameworks\Contracts\HasFrameworkRequirements;
use Trustbird\Frameworks\Models\Concerns\InteractsWithFrameworkRequirements;

final class FrameworkRequirement extends Model implements HasFrameworkRequirements
{
    use HasFactory, InteractsWithFrameworkRequirements {
        InteractsWithFrameworkRequirements::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'framework_requirements';

    protected $attributes = [
        'position' => 0,
    ];
}
