<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Facades\Trustbird;
use Trustbird\Workspaces\Models\Workspace;

beforeEach(fn () => Event::fake());

it('can create a workspace via the facade', function () {
    $workspace = Trustbird::workspaces()->create(
        name: 'Main HasWorkspaces',
        slug: 'main'
    );

    expect($workspace)->toBeInstanceOf(Workspace::class)
        ->and($workspace->name)->toBe('Main HasWorkspaces')
        ->and($workspace->slug)->toBe('main');

    Event::assertDispatched('eloquent.created: '.Workspace::class);
});

it('can update a workspace via the facade', function () {
    $workspace = Workspace::factory()->create(['name' => 'Old HasWorkspaces']);

    $updated = Trustbird::workspaces()->update(
        $workspace,
        name: 'New HasWorkspaces',
        slug: 'new-slug',
        description: 'New Description',
        metadata: ['custom' => 'data']
    );

    expect($updated->name)->toBe('New HasWorkspaces')
        ->and($updated->slug)->toBe('new-slug')
        ->and($updated->description)->toBe('New Description')
        ->and($updated->metadata)->toBe(['custom' => 'data']);

    Event::assertDispatched('eloquent.updated: '.Workspace::class);
});

it('can archive a workspace via the facade', function () {
    $workspace = Workspace::factory()->create();

    $archived = Trustbird::workspaces()->archive($workspace);

    expect($archived->metadata)->toHaveKey('archived_at');
    Event::assertDispatched('eloquent.updated: '.Workspace::class);
});
