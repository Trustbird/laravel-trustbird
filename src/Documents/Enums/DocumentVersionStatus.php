<?php

declare(strict_types=1);

namespace Trustbird\Documents\Enums;

enum DocumentVersionStatus: string
{
    case Draft = 'draft';

    case Published = 'published';

    case Superseded = 'superseded';
}
