<?php

declare(strict_types=1);

namespace Trustbird\Reviews\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Reviews\Contracts\HasReviews;
use Trustbird\Reviews\Models\Concerns\InteractsWithReviews;

final class Review extends Model implements HasReviews
{
    use HasFactory, InteractsWithReviews {
        InteractsWithReviews::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'reviews';

    protected $attributes = [
        'status' => 'scheduled',
    ];
}
