<?php

declare(strict_types=1);

namespace Trustbird\Tests\Feature;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Trustbird\Assets\Managers\AssetsManager;
use Trustbird\Database\Factories\Person\PersonFactory;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\People\Managers\PeopleManager;
use Trustbird\People\Models\Concerns\InteractsWithPeople;
use Trustbird\People\Models\Person;
use Trustbird\Risks\Managers\RisksManager;
use Trustbird\Teams\Managers\TeamsManager;
use Trustbird\Tests\TestCase;
use Trustbird\TrustbirdManager;
use Trustbird\Workspaces\Managers\WorkspacesManager;
use Trustbird\Workspaces\Models\Workspace;

class DeveloperApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_facade_works(): void
    {
        $this->assertInstanceOf(PeopleManager::class, Trustbird::people());
        $this->assertInstanceOf(WorkspacesManager::class, Trustbird::workspaces());
        $this->assertInstanceOf(AssetsManager::class, Trustbird::assets());
        $this->assertInstanceOf(TeamsManager::class, Trustbird::teams());
        $this->assertInstanceOf(RisksManager::class, Trustbird::risks());
    }

    public function test_helper_works(): void
    {
        $this->assertInstanceOf(TrustbirdManager::class, trustbird());
        $this->assertInstanceOf(PeopleManager::class, trustbird()->people());
    }

    public function test_can_create_person_through_api(): void
    {
        $workspace = Workspace::factory()->create();

        $person = Trustbird::people()->create(
            firstName: 'Jane',
            lastName: 'Doe',
            email: 'jane@example.com',
            workspaceId: $workspace->id
        );

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals('Jane', $person->first_name);
        $this->assertEquals('Doe', $person->last_name);
        $this->assertEquals('jane@example.com', $person->email);
        $this->assertEquals($workspace->id, $person->workspace_id);
    }

    public function test_can_replace_models_via_config(): void
    {
        // Define a custom model class using interface and trait
        $customModel = new class extends Model implements HasPeople
        {
            use HasFactory;
            use HasUlids;
            use InteractsWithPeople;

            protected $table = 'people';

            protected static function newFactory(): PersonFactory
            {
                return PersonFactory::new();
            }
        };

        // In a test, we need to bind the interface to our anonymous class
        $this->app->bind(HasPeople::class, get_class($customModel));

        $person = Trustbird::people()->create(
            firstName: 'Custom',
            lastName: 'Model',
            email: 'custom@example.com',
        );

        $this->assertInstanceOf(get_class($customModel), $person);
        $this->assertInstanceOf(HasPeople::class, $person);
        $this->assertEquals('Custom', $person->first_name);
    }
}
