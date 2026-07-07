<?php

declare(strict_types=1);

namespace Trustbird\Reviews\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Reviews\Contracts\HasReviewReviewers;
use Trustbird\Reviews\Models\Concerns\InteractsWithReviewReviewers;

final class ReviewReviewer extends Model implements HasReviewReviewers
{
    use HasFactory, InteractsWithReviewReviewers {
        InteractsWithReviewReviewers::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'review_reviewers';

    protected $attributes = [
        'role' => 'primary',
    ];
}
