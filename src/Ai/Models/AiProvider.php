<?php

declare(strict_types=1);

namespace Trustbird\Ai\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Ai\Contracts\HasAiProviders;
use Trustbird\Ai\Models\Concerns\InteractsWithAiProviders;

final class AiProvider extends Model implements HasAiProviders
{
    use HasFactory, InteractsWithAiProviders {
        InteractsWithAiProviders::newFactory insteadof HasFactory;
    }
    use HasUlids;

    protected $table = 'ai_providers';

    protected $attributes = [
        'driver' => 'custom',
        'is_active' => true,
    ];
}
