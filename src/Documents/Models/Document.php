<?php

declare(strict_types=1);

namespace Trustbird\Documents\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Documents\Contracts\HasDocuments;
use Trustbird\Documents\Models\Concerns\InteractsWithDocuments;

final class Document extends Model implements HasDocuments
{
    use HasFactory, InteractsWithDocuments {
        InteractsWithDocuments::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'documents';
}
