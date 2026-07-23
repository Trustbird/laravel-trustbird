<?php

declare(strict_types=1);

namespace Trustbird\Evidence\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Evidence\Contracts\HasEvidence;
use Trustbird\Evidence\Models\Concerns\InteractsWithEvidence;

final class Evidence extends Model implements HasEvidence
{
    use HasFactory, InteractsWithEvidence {
        InteractsWithEvidence::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'evidence';

    protected $attributes = [
        'type' => 'other',
        'status' => 'draft',
    ];
}
