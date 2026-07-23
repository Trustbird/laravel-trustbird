<?php

declare(strict_types=1);

namespace Trustbird\Evidence\Enums;

enum EvidenceType: string
{
    case Document = 'document';

    case Link = 'link';

    case Upload = 'upload';

    case Note = 'note';

    case Other = 'other';
}
