<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Enums;

enum InterviewStatus: string
{
    case Draft = 'draft';

    case InProgress = 'in_progress';

    case Completed = 'completed';

    case Archived = 'archived';
}
