<?php

declare(strict_types=1);

namespace Trustbird\Policies\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Policies\Contracts\HasPolicies;
use Trustbird\Policies\Models\Concerns\InteractsWithPolicies;

final class Policy extends Model implements HasPolicies
{
    use HasFactory, InteractsWithPolicies {
        InteractsWithPolicies::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'policies';
}
