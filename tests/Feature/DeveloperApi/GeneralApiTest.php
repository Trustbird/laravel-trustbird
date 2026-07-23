<?php

declare(strict_types=1);

use Trustbird\Assets\Managers\AssetsManager;
use Trustbird\Controls\Managers\ControlsManager;
use Trustbird\Documents\Managers\DocumentsManager;
use Trustbird\Evidence\Managers\EvidenceManager;
use Trustbird\Facades\Trustbird;
use Trustbird\Incidents\Managers\IncidentsManager;
use Trustbird\People\Managers\PeopleManager;
use Trustbird\Policies\Managers\PoliciesManager;
use Trustbird\Reviews\Managers\ReviewsManager;
use Trustbird\Risks\Managers\RisksManager;
use Trustbird\Suppliers\Managers\SuppliersManager;
use Trustbird\Tasks\Managers\TasksManager;
use Trustbird\Teams\Managers\TeamsManager;
use Trustbird\TrustbirdManager;
use Trustbird\Workspaces\Managers\WorkspacesManager;

it('provides access to all managers via the facade', function () {
    expect(Trustbird::people())->toBeInstanceOf(PeopleManager::class)
        ->and(Trustbird::workspaces())->toBeInstanceOf(WorkspacesManager::class)
        ->and(Trustbird::assets())->toBeInstanceOf(AssetsManager::class)
        ->and(Trustbird::controls())->toBeInstanceOf(ControlsManager::class)
        ->and(Trustbird::documents())->toBeInstanceOf(DocumentsManager::class)
        ->and(Trustbird::evidence())->toBeInstanceOf(EvidenceManager::class)
        ->and(Trustbird::teams())->toBeInstanceOf(TeamsManager::class)
        ->and(Trustbird::risks())->toBeInstanceOf(RisksManager::class)
        ->and(Trustbird::reviews())->toBeInstanceOf(ReviewsManager::class)
        ->and(Trustbird::policies())->toBeInstanceOf(PoliciesManager::class)
        ->and(Trustbird::incidents())->toBeInstanceOf(IncidentsManager::class)
        ->and(Trustbird::suppliers())->toBeInstanceOf(SuppliersManager::class)
        ->and(Trustbird::tasks())->toBeInstanceOf(TasksManager::class);
});

it('provides access to all managers via the helper', function () {
    expect(trustbird())->toBeInstanceOf(TrustbirdManager::class)
        ->and(trustbird()->people())->toBeInstanceOf(PeopleManager::class)
        ->and(trustbird()->workspaces())->toBeInstanceOf(WorkspacesManager::class)
        ->and(trustbird()->assets())->toBeInstanceOf(AssetsManager::class)
        ->and(trustbird()->controls())->toBeInstanceOf(ControlsManager::class)
        ->and(trustbird()->documents())->toBeInstanceOf(DocumentsManager::class)
        ->and(trustbird()->evidence())->toBeInstanceOf(EvidenceManager::class)
        ->and(trustbird()->teams())->toBeInstanceOf(TeamsManager::class)
        ->and(trustbird()->risks())->toBeInstanceOf(RisksManager::class)
        ->and(trustbird()->reviews())->toBeInstanceOf(ReviewsManager::class)
        ->and(trustbird()->policies())->toBeInstanceOf(PoliciesManager::class)
        ->and(trustbird()->incidents())->toBeInstanceOf(IncidentsManager::class)
        ->and(trustbird()->suppliers())->toBeInstanceOf(SuppliersManager::class)
        ->and(trustbird()->tasks())->toBeInstanceOf(TasksManager::class);
});
