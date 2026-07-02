<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Models\Workspace;

beforeEach(function () {
    config(['trustbird.multi_tenant' => false]);
});

test('it automatically assigns the first workspace in single tenant mode', function () {
    $workspace = Workspace::factory()->create();
    
    // Maak een person zonder workspace_id
    $person = Person::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'employment_type' => \Trustbird\People\Enums\EmploymentType::Employee,
        'employment_status' => \Trustbird\People\Enums\EmploymentStatus::Active,
    ]);

    expect($person->workspace_id)->toBe($workspace->id);
});

test('it respects provided workspace_id in single tenant mode', function () {
    $workspace1 = Workspace::factory()->create();
    $workspace2 = Workspace::factory()->create();
    
    $person = Person::create([
        'workspace_id' => $workspace2->id,
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@example.com',
        'employment_type' => \Trustbird\People\Enums\EmploymentType::Employee,
        'employment_status' => \Trustbird\People\Enums\EmploymentStatus::Active,
    ]);

    expect($person->workspace_id)->toBe($workspace2->id);
});

test('it throws exception in multi tenant mode when workspace_id is missing', function () {
    config(['trustbird.multi_tenant' => true]);
    
    Workspace::factory()->create();

    Person::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'employment_type' => \Trustbird\People\Enums\EmploymentType::Employee,
        'employment_status' => \Trustbird\People\Enums\EmploymentStatus::Active,
    ]);
})->throws(\RuntimeException::class, 'A workspace_id is required when multi-tenancy is enabled.');

test('it allows creation with workspace_id in multi tenant mode', function () {
    config(['trustbird.multi_tenant' => true]);
    
    $workspace = Workspace::factory()->create();

    $person = Person::create([
        'workspace_id' => $workspace->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'employment_type' => \Trustbird\People\Enums\EmploymentType::Employee,
        'employment_status' => \Trustbird\People\Enums\EmploymentStatus::Active,
    ]);

    expect($person->workspace_id)->toBe($workspace->id);
});

test('it restores workspace_id on update in single tenant mode if cleared', function () {
    $workspace = Workspace::factory()->create();
    $person = Person::factory()->create(['workspace_id' => $workspace->id]);
    
    $person->workspace_id = null;
    $person->save();

    expect($person->workspace_id)->toBe($workspace->id);
});

test('it can install default workspace', function () {
    expect(Workspace::count())->toBe(0);

    Artisan::call('trustbird:install');

    expect(Workspace::count())->toBe(1);
    expect(Workspace::first()->slug)->toBe('default');

    // Tweede keer draaien zou niets extra moeten doen
    Artisan::call('trustbird:install');
    expect(Workspace::count())->toBe(1);
});
