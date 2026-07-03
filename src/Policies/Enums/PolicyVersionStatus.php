<?php

declare(strict_types=1);

namespace Trustbird\Policies\Enums;

enum PolicyVersionStatus: string
{
    case Draft = 'draft';

    case Published = 'published';

    case Superseded = 'superseded';
}
