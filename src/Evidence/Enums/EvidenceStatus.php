<?php

declare(strict_types=1);

namespace Trustbird\Evidence\Enums;

enum EvidenceStatus: string
{
    case Draft = 'draft';

    case Active = 'active';

    case UnderReview = 'under_review';

    case Archived = 'archived';
}
