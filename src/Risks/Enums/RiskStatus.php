<?php

declare(strict_types=1);

namespace Trustbird\Risks\Enums;

enum RiskStatus: string
{
    case Open = 'open';

    case UnderReview = 'under_review';

    case BeingAddressed = 'being_addressed';

    case Accepted = 'accepted';

    case Resolved = 'resolved';

    case Archived = 'archived';
}
