<?php

declare(strict_types=1);

namespace Trustbird;

use Illuminate\Support\ServiceProvider;

final class TrustbirdServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/trustbird.php', 'trustbird');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Workspaces\Commands\InstallCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/trustbird.php' => config_path('trustbird.php'),
            ], 'trustbird-config');
        }
    }
}