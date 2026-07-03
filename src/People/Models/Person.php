<?php

declare(strict_types=1);

namespace Trustbird\People\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\People\Models\Concerns\InteractsWithPeople;

final class Person extends Model implements HasPeople
{
    use HasFactory, InteractsWithPeople {
        InteractsWithPeople::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'people';
}
