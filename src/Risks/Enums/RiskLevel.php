<?php

declare(strict_types=1);

namespace Trustbird\Risks\Enums;

enum RiskLevel: string
{
    case Low = 'low';

    case Medium = 'medium';

    case High = 'high';
}
