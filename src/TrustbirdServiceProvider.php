<?php

declare(strict_types=1);

namespace Trustbird;

use Illuminate\Support\ServiceProvider;
use Trustbird\Assets\Contracts\HasAssets;
use Trustbird\Assets\Models\Asset;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\People\Models\Person;
use Trustbird\Risks\Contracts\HasRisks;
use Trustbird\Risks\Models\Risk;
use Trustbird\Policies\Contracts\HasPolicies;
use Trustbird\Policies\Models\Policy;
use Trustbird\Suppliers\Contracts\HasSupplierRelations;
use Trustbird\Suppliers\Contracts\HasSuppliers;
use Trustbird\Suppliers\Models\Supplier;
use Trustbird\Suppliers\Models\SupplierRelation;
use Trustbird\Teams\Contracts\HasTeams;
use Trustbird\Teams\Models\Team;
use Trustbird\Workspaces\Contracts\HasWorkspaces;
use Trustbird\Workspaces\Models\Workspace;

final class TrustbirdServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/trustbird.php', 'trustbird');

        $this->app->singleton('trustbird', function ($app) {
            return new TrustbirdManager;
        });

        $this->registerModels();
    }

    protected function registerModels(): void
    {
        $models = [
            'person' => [
                'contract' => HasPeople::class,
                'default' => Person::class,
            ],
            'workspace' => [
                'contract' => HasWorkspaces::class,
                'default' => Workspace::class,
            ],
            'asset' => [
                'contract' => HasAssets::class,
                'default' => Asset::class,
            ],
            'team' => [
                'contract' => HasTeams::class,
                'default' => Team::class,
            ],
            'risk' => [
                'contract' => HasRisks::class,
                'default' => Risk::class,
            ],
            'policy' => [
                'contract' => HasPolicies::class,
                'default' => Policy::class,
            ],
            'supplier' => [
                'contract' => HasSuppliers::class,
                'default' => Supplier::class,
            ],
            'supplier_relation' => [
                'contract' => HasSupplierRelations::class,
                'default' => SupplierRelation::class,
            ],
        ];

        foreach ($models as $key => $config) {
            $concrete = $this->app['config']["trustbird.models.{$key}"] ?? $config['default'];

            $this->app->bind($config['contract'], $concrete);

            if ($concrete !== $config['default']) {
                $this->app->bind($config['default'], $concrete);
            }
        }
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Workspaces\Commands\InstallCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/trustbird.php' => config_path('trustbird.php'),
            ], 'trustbird-config');
        }
    }
}
