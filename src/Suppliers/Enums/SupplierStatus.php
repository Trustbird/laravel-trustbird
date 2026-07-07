<?php

declare(strict_types=1);

namespace Trustbird\Suppliers\Enums;

enum SupplierStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Offboarded = 'offboarded';
}

