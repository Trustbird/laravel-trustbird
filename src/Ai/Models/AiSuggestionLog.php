<?php

declare(strict_types=1);

namespace Trustbird\Ai\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Ai\Contracts\HasAiSuggestionLogs;
use Trustbird\Ai\Models\Concerns\InteractsWithAiSuggestionLogs;

final class AiSuggestionLog extends Model implements HasAiSuggestionLogs
{
    use HasFactory, InteractsWithAiSuggestionLogs {
        InteractsWithAiSuggestionLogs::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'ai_suggestion_logs';
}
