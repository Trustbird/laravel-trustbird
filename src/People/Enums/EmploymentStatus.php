<?php

declare(strict_types=1);

namespace Trustbird\People\Enums;

enum EmploymentStatus: string
{
    case Active = 'active';

    case Pending = 'pending';

    case Offboarding = 'offboarding';

    case Terminated = 'terminated';
}
