<?php

declare(strict_types=1);

namespace Trustbird\Reviews\Enums;

enum ReviewStatus: string
{
    case Scheduled = 'scheduled';

    case Completed = 'completed';

    case Reopened = 'reopened';
}
