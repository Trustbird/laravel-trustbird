<?php

declare(strict_types=1);

namespace Trustbird\Incidents\Enums;

enum IncidentStatus: string
{
    case Open = 'open';
    case Investigating = 'investigating';
    case Contained = 'contained';
    case Resolved = 'resolved';
    case Archived = 'archived';
}

