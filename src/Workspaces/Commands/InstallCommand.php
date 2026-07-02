<?php

declare(strict_types=1);

namespace Trustbird\Workspaces\Commands;

use Illuminate\Console\Command;
use Trustbird\Workspaces\Models\Workspace;

final class InstallCommand extends Command
{
    protected $signature = 'trustbird:install';

    protected $description = 'Install Trustbird and create the default workspace';

    public function handle(): int
    {
        $this->info('Installing Trustbird...');

        if (Workspace::query()->count() === 0) {
            Workspace::query()->create([
                'name' => 'Default Workspace',
                'slug' => 'default',
                'description' => 'Automatically created default workspace',
            ]);

            $this->info('Default workspace created.');
        } else {
            $this->info('Workspaces already exist, skipping default workspace creation.');
        }

        $this->info('Trustbird installed successfully.');

        return self::SUCCESS;
    }
}
