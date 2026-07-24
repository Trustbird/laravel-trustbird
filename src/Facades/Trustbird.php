<?php

declare(strict_types=1);

namespace Trustbird\Facades;

use Illuminate\Support\Facades\Facade;
use Trustbird\TrustbirdManager;

/**
 * @method static \Trustbird\People\Managers\PeopleManager people()
 * @method static \Trustbird\Workspaces\Managers\WorkspacesManager workspaces()
 * @method static \Trustbird\Assets\Managers\AssetsManager assets()
 * @method static \Trustbird\Ai\Managers\AiManager ai()
 * @method static \Trustbird\Controls\Managers\ControlsManager controls()
 * @method static \Trustbird\Documents\Managers\DocumentsManager documents()
 * @method static \Trustbird\Evidence\Managers\EvidenceManager evidence()
 * @method static \Trustbird\Frameworks\Managers\FrameworksManager frameworks()
 * @method static \Trustbird\Teams\Managers\TeamsManager teams()
 * @method static \Trustbird\Risks\Managers\RisksManager risks()
 * @method static \Trustbird\Reviews\Managers\ReviewsManager reviews()
 * @method static \Trustbird\Policies\Managers\PoliciesManager policies()
 * @method static \Trustbird\Incidents\Managers\IncidentsManager incidents()
 * @method static \Trustbird\Interviews\Managers\InterviewsManager interviews()
 * @method static \Trustbird\Suppliers\Managers\SuppliersManager suppliers()
 * @method static \Trustbird\Tasks\Managers\TasksManager tasks()
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
