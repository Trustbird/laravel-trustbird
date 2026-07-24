<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Enums;

enum InterviewSuggestionDomain: string
{
    case Policy = 'policy';

    case Risk = 'risk';

    case Measure = 'measure';

    case Evidence = 'evidence';
}
