<?php

declare(strict_types=1);

namespace Trustbird\Reviews\Enums;

enum ReviewerRole: string
{
    case Primary = 'primary';

    case Contributor = 'contributor';

    case Observer = 'observer';
}
