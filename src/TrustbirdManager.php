<?php

declare(strict_types=1);

namespace Trustbird;

use Trustbird\Ai\Managers\AiManager;
use Trustbird\Assets\Managers\AssetsManager;
use Trustbird\Controls\Managers\ControlsManager;
use Trustbird\Documents\Managers\DocumentsManager;
use Trustbird\Evidence\Managers\EvidenceManager;
use Trustbird\Frameworks\Managers\FrameworksManager;
use Trustbird\Incidents\Managers\IncidentsManager;
use Trustbird\Interviews\Managers\InterviewsManager;
use Trustbird\People\Managers\PeopleManager;
use Trustbird\Policies\Managers\PoliciesManager;
use Trustbird\Reviews\Managers\ReviewsManager;
use Trustbird\Risks\Managers\RisksManager;
use Trustbird\Suppliers\Managers\SuppliersManager;
use Trustbird\Tasks\Managers\TasksManager;
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

    public function ai(): AiManager
    {
        return new AiManager;
    }

    public function controls(): ControlsManager
    {
        return new ControlsManager;
    }

    public function documents(): DocumentsManager
    {
        return new DocumentsManager;
    }

    public function evidence(): EvidenceManager
    {
        return new EvidenceManager;
    }

    public function frameworks(): FrameworksManager
    {
        return new FrameworksManager;
    }

    public function teams(): TeamsManager
    {
        return new TeamsManager;
    }

    public function risks(): RisksManager
    {
        return new RisksManager;
    }

    public function reviews(): ReviewsManager
    {
        return new ReviewsManager;
    }

    public function policies(): PoliciesManager
    {
        return new PoliciesManager;
    }

    public function incidents(): IncidentsManager
    {
        return new IncidentsManager;
    }

    public function interviews(): InterviewsManager
    {
        return new InterviewsManager;
    }

    public function suppliers(): SuppliersManager
    {
        return new SuppliersManager;
    }

    public function tasks(): TasksManager
    {
        return new TasksManager;
    }
}
