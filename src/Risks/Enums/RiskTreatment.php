<?php

declare(strict_types=1);

namespace Trustbird\Risks\Enums;

enum RiskTreatment: string
{
    case Accept = 'accept';

    case Reduce = 'reduce';

    case Avoid = 'avoid';

    case Transfer = 'transfer';

    case Monitor = 'monitor';
}
