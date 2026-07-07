<?php

declare(strict_types=1);

namespace Trustbird\Facades;

use Illuminate\Support\Facades\Facade;
use Trustbird\TrustbirdManager;

/**
 * @method static \Trustbird\People\Managers\PeopleManager people()
 * @method static \Trustbird\Workspaces\Managers\WorkspacesManager workspaces()
 * @method static \Trustbird\Assets\Managers\AssetsManager assets()
 * @method static \Trustbird\Teams\Managers\TeamsManager teams()
 * @method static \Trustbird\Risks\Managers\RisksManager risks()
 * @method static \Trustbird\Policies\Managers\PoliciesManager policies()
 * @method static \Trustbird\Suppliers\Managers\SuppliersManager suppliers()
 *
 * @see TrustbirdManager
 */
final class Trustbird extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'trustbird';
    }
}
