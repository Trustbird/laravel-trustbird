<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Workspaces\Actions\CreateWorkspace;
use Trustbird\Workspaces\Actions\UpdateWorkspace;
use Trustbird\Workspaces\Events\WorkspaceCreated;
use Trustbird\Workspaces\Events\WorkspaceUpdated;
use Trustbird\Workspaces\Models\Workspace;
use Trustbird\People\Models\Person;
use Trustbird\Assets\Models\Asset;

test('it can create a workspace and dispatches event', function () {
    Event::fake();

    $action = new CreateWorkspace();
    $workspace = $action->handle([
        'name' => 'Acme Corp',
        'slug' => 'acme-corp',
        'description' => 'Main workspace for Acme Corp',
    ]);

    expect($workspace)->toBeInstanceOf(Workspace::class)
        ->name->toBe('Acme Corp')
        ->slug->toBe('acme-corp')
        ->description->toBe('Main workspace for Acme Corp');

    Event::assertDispatched(WorkspaceCreated::class, function ($event) use ($workspace) {
        return $event->workspace->id === $workspace->id;
    });
});

test('it can update a workspace and dispatches event', function () {
    Event::fake();

    $workspace = Workspace::factory()->create(['name' => 'Old Name']);

    $action = new UpdateWorkspace();
    $updatedWorkspace = $action->handle($workspace, ['name' => 'New Name']);

    expect($updatedWorkspace->name)->toBe('New Name');

    Event::assertDispatched(WorkspaceUpdated::class, function ($event) use ($workspace) {
        return $event->workspace->id === $workspace->id;
    });
});

test('it covers workspace model factory', function () {
    $workspace = Workspace::factory()->create();

    expect($workspace)->toBeInstanceOf(Workspace::class)
        ->name->toBeString()
        ->slug->toBeString();
    
    expect(Workspace::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Workspace\WorkspaceFactory::class);
});

test('it has relationships to people and assets', function () {
    $workspace = Workspace::factory()->create();
    
    $person = Person::factory()->create(['workspace_id' => $workspace->id]);
    $asset = Asset::factory()->create(['workspace_id' => $workspace->id]);
    
    expect($workspace->people)->toHaveCount(1)
        ->and($workspace->people->first()->id)->toBe($person->id);
        
    expect($workspace->assets)->toHaveCount(1)
        ->and($workspace->assets->first()->id)->toBe($asset->id);
        
    expect($person->workspace->id)->toBe($workspace->id);
    expect($asset->workspace->id)->toBe($workspace->id);
});
