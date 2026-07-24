<?php

declare(strict_types=1);

namespace Trustbird\Ai\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Ai\Contracts\HasAiPrompts;
use Trustbird\Ai\Models\Concerns\InteractsWithAiPrompts;

final class AiPrompt extends Model implements HasAiPrompts
{
    use HasFactory, InteractsWithAiPrompts {
        InteractsWithAiPrompts::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'ai_prompts';
}
