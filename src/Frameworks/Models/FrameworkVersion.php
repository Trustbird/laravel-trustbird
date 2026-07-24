<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Frameworks\Contracts\HasFrameworkVersions;
use Trustbird\Frameworks\Models\Concerns\InteractsWithFrameworkVersions;

final class FrameworkVersion extends Model implements HasFrameworkVersions
{
    use HasFactory, InteractsWithFrameworkVersions {
        InteractsWithFrameworkVersions::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'framework_versions';

    protected $attributes = [
        'status' => 'draft',
    ];
}
