<?php

declare(strict_types=1);

namespace Trustbird\Controls\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Controls\Contracts\HasControls;
use Trustbird\Controls\Models\Concerns\InteractsWithControls;

final class Control extends Model implements HasControls
{
    use HasFactory, InteractsWithControls {
        InteractsWithControls::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'controls';

    protected $attributes = [
        'status' => 'draft',
    ];
}
