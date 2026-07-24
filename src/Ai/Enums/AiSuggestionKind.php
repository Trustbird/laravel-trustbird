<?php

declare(strict_types=1);

namespace Trustbird\Ai\Enums;

enum AiSuggestionKind: string
{
    case Policy = 'policy';

    case Risk = 'risk';

    case Measure = 'measure';

    case Evidence = 'evidence';

    case General = 'general';
}
