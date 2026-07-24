<?php

declare(strict_types=1);

namespace Trustbird\Ai\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trustbird\Ai\Contracts\HasAiSuggestions;

final class AiSuggestionRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public HasAiSuggestions $suggestion,
    ) {}
}
