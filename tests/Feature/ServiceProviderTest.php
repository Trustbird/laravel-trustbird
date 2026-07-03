<?php

declare(strict_types=1);

namespace Trustbird\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\People\Models\Concerns\InteractsWithPeople;
use Trustbird\People\Models\Person;
use Trustbird\Tests\TestCase;
use Trustbird\TrustbirdServiceProvider;

class ServiceProviderTest extends TestCase
{
    public function test_it_binds_default_models(): void
    {
        $this->assertInstanceOf(Person::class, $this->app->make(HasPeople::class));
    }

    public function test_it_can_override_models_via_config(): void
    {
        $customModel = new class extends Model implements HasPeople
        {
            use InteractsWithPeople;

            protected $table = 'people';
        };
        $customClass = get_class($customModel);

        Config::set('trustbird.models.person', $customClass);

        // Re-register the service provider to apply new config
        (new TrustbirdServiceProvider($this->app))->register();

        $this->assertInstanceOf($customClass, $this->app->make(HasPeople::class));
        $this->assertInstanceOf($customClass, $this->app->make(Person::class));
    }

    public function test_it_publishes_config(): void
    {
        $this->artisan('vendor:publish', [
            '--tag' => 'trustbird-config',
            '--force' => true,
        ])->assertExitCode(0);

        $this->assertFileExists(config_path('trustbird.php'));
    }
}
