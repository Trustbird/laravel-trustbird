<?php

declare(strict_types=1);

namespace Trustbird;

use Trustbird\Assets\Managers\AssetsManager;
use Trustbird\Incidents\Managers\IncidentsManager;
use Trustbird\People\Managers\PeopleManager;
use Trustbird\Policies\Managers\PoliciesManager;
use Trustbird\Risks\Managers\RisksManager;
use Trustbird\Teams\Managers\TeamsManager;
use Trustbird\Workspaces\Managers\WorkspacesManager;

final class TrustbirdManager
{
    public function people(): PeopleManager
    {
        return new PeopleManager;
    }

    public function workspaces(): WorkspacesManager
    {
        return new WorkspacesManager;
    }

    public function assets(): AssetsManager
    {
        return new AssetsManager;
    }

    public function teams(): TeamsManager
    {
        return new TeamsManager;
    }

    public function risks(): RisksManager
    {
        return new RisksManager;
    }

    public function policies(): PoliciesManager
    {
        return new PoliciesManager;
    }

    public function incidents(): IncidentsManager
    {
        return new IncidentsManager;
    }
}
