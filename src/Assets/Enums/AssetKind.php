<?php

declare(strict_types=1);

namespace Trustbird\Assets\Enums;

enum AssetKind: string
{
    case Device = 'device';
    case System = 'system';
    case Application = 'application';
    case DataStore = 'data_store';
    case Service = 'service';
    case Account = 'account';
    case Location = 'location';
    case Other = 'other';
}
