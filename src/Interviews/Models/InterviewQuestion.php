<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Interviews\Contracts\HasInterviewQuestions;
use Trustbird\Interviews\Models\Concerns\InteractsWithInterviewQuestions;

final class InterviewQuestion extends Model implements HasInterviewQuestions
{
    use HasFactory, InteractsWithInterviewQuestions {
        InteractsWithInterviewQuestions::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'interview_questions';

    protected $attributes = [
        'type' => 'text',
        'position' => 0,
        'is_required' => true,
    ];
}
