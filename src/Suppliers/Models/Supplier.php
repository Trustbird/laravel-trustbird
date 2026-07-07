<?php

declare(strict_types=1);

namespace Trustbird\Suppliers\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Suppliers\Contracts\HasSuppliers;
use Trustbird\Suppliers\Models\Concerns\InteractsWithSuppliers;

final class Supplier extends Model implements HasSuppliers
{
    use HasFactory, InteractsWithSuppliers {
        InteractsWithSuppliers::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'suppliers';

    protected $attributes = [
        'status' => 'active',
        'criticality' => 'medium',
    ];
}

