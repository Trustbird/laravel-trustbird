<?php

declare(strict_types=1);

namespace Trustbird\People\Enums;

enum EmploymentType: string
{
    case Employee = 'employee';

    case Contractor = 'contractor';

    case Freelancer = 'freelancer';

    case Advisor = 'advisor';

    case Intern = 'intern';
}