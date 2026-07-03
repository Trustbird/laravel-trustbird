<?php

declare(strict_types=1);

namespace Trustbird\Assets\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Assets\Contracts\HasAssets;
use Trustbird\Assets\Models\Concerns\InteractsWithAssets;

final class Asset extends Model implements HasAssets
{
    use HasFactory, InteractsWithAssets {
        InteractsWithAssets::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'assets';
}
