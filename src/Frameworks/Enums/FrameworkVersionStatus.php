<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Enums;

enum FrameworkVersionStatus: string
{
    case Draft = 'draft';

    case Published = 'published';

    case Superseded = 'superseded';
}
