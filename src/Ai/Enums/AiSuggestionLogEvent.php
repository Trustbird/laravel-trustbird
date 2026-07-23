<?php

declare(strict_types=1);

namespace Trustbird\Ai\Enums;

enum AiSuggestionLogEvent: string
{
    case Created = 'created';

    case Approved = 'approved';

    case Rejected = 'rejected';

    case Withdrawn = 'withdrawn';

    case Updated = 'updated';
}
