<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Interviews\Contracts\HasInterviewAnswers;
use Trustbird\Interviews\Models\Concerns\InteractsWithInterviewAnswers;

final class InterviewAnswer extends Model implements HasInterviewAnswers
{
    use HasFactory, InteractsWithInterviewAnswers {
        InteractsWithInterviewAnswers::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'interview_answers';
}
