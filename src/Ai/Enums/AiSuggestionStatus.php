<?php

declare(strict_types=1);

namespace Trustbird\Ai\Enums;

enum AiSuggestionStatus: string
{
    case Pending = 'pending';

    case Approved = 'approved';

    case Rejected = 'rejected';

    case Withdrawn = 'withdrawn';
}
