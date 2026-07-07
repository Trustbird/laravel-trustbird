<?php

declare(strict_types=1);

namespace Trustbird\Suppliers\Enums;

enum SupplierCriticality: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Critical = 'critical';
}

