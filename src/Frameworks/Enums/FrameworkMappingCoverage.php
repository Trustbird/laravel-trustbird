<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Enums;

enum FrameworkMappingCoverage: string
{
    case Full = 'full';

    case Partial = 'partial';

    case Planned = 'planned';

    case None = 'none';
}
