<?php

declare(strict_types=1);

namespace Trustbird\Ai\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Ai\Contracts\HasAiSuggestions;
use Trustbird\Ai\Models\Concerns\InteractsWithAiSuggestions;

final class AiSuggestion extends Model implements HasAiSuggestions
{
    use HasFactory, InteractsWithAiSuggestions {
        InteractsWithAiSuggestions::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'ai_suggestions';

    protected $attributes = [
        'kind' => 'general',
        'status' => 'pending',
    ];
}
