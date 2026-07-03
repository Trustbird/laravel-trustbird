<?php

declare(strict_types=1);

namespace Trustbird\Workspaces\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Trustbird\Workspaces\Contracts\HasWorkspaces;

final class InstallCommand extends Command
{
    protected $signature = 'trustbird:install';

    protected $description = 'Install Trustbird and create the default workspace';

    public function handle(): int
    {
        $this->info('Installing Trustbird...');

        /** @var Model $model */
        $model = app(HasWorkspaces::class);

        if ($model->newQuery()->count() === 0) {
            $model->newQuery()->create([
                'name' => 'Default HasWorkspaces',
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
