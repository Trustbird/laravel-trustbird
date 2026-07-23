<?php

declare(strict_types=1);

namespace Trustbird\Controls\Enums;

enum ControlStatus: string
{
    case Draft = 'draft';

    case Active = 'active';

    case Inactive = 'inactive';

    case UnderReview = 'under_review';
}
