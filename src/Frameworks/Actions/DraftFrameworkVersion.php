<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Actions;

use Trustbird\Frameworks\Contracts\HasFrameworks;
use Trustbird\Frameworks\Contracts\HasFrameworkVersions;
use Trustbird\Frameworks\Enums\FrameworkVersionStatus;
use Trustbird\Frameworks\Events\FrameworkVersionDrafted;

final readonly class DraftFrameworkVersion
{
    /**
     * @param array{
     *     version_label: string,
     *     change_summary?: string|null,
     *     metadata?: array|null,
     * } $attributes
     */
    public function handle(HasFrameworks $framework, array $attributes): HasFrameworkVersions
    {
        /** @var HasFrameworkVersions $model */
        $model = app(HasFrameworkVersions::class);

        $version = $model->query()->create([
            'workspace_id' => $framework->workspace_id,
            'framework_id' => $framework->id,
            'version_label' => $attributes['version_label'],
            'status' => FrameworkVersionStatus::Draft,
            'change_summary' => $attributes['change_summary'] ?? null,
            'metadata' => $attributes['metadata'] ?? null,
        ]);

        FrameworkVersionDrafted::dispatch($framework, $version);

        return $version;
    }
}
