<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Interviews\Contracts\HasInterviews;
use Trustbird\Interviews\Models\Concerns\InteractsWithInterviews;

final class Interview extends Model implements HasInterviews
{
    use HasFactory, InteractsWithInterviews {
        InteractsWithInterviews::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'interviews';

    protected $attributes = [
        'status' => 'draft',
        'answered_count' => 0,
        'question_count' => 0,
    ];
}
