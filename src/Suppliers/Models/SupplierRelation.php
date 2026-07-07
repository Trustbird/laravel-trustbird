<?php

declare(strict_types=1);

namespace Trustbird\Suppliers\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Suppliers\Contracts\HasSupplierRelations;
use Trustbird\Suppliers\Models\Concerns\InteractsWithSupplierRelations;

final class SupplierRelation extends Model implements HasSupplierRelations
{
    use HasFactory, InteractsWithSupplierRelations {
        InteractsWithSupplierRelations::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'supplier_relations';
}

