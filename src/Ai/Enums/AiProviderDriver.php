<?php

declare(strict_types=1);

namespace Trustbird\Ai\Enums;

enum AiProviderDriver: string
{
    case OpenAi = 'openai';

    case Anthropic = 'anthropic';

    case Azure = 'azure';

    case Custom = 'custom';
}
