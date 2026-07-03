<?php

declare(strict_types=1);

namespace Trustbird\Workspaces\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Trustbird\Workspaces\Contracts\HasWorkspaces;

trait BelongsToWorkspace
{
    public static function bootBelongsToWorkspace(): void
    {
        static::saving(function (Model $model) {
            if (empty($model->workspace_id)) {
                if (config('trustbird.multi_tenant', false)) {
                    throw new \RuntimeException('A workspace_id is required when multi-tenancy is enabled.');
                }

                /** @var Model $workspaceModel */
                $workspaceModel = app(HasWorkspaces::class);
                $model->workspace_id = $workspaceModel->newQuery()->first()?->id;
            }
        });
    }

    public function initializeBelongsToWorkspace(): void
    {
        $this->mergeFillable(['workspace_id']);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(app(HasWorkspaces::class)::class);
    }
}
