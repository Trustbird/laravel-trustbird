<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Actions;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Trustbird\Frameworks\Contracts\HasFrameworks;
use Trustbird\Frameworks\Contracts\HasFrameworkVersions;
use Trustbird\Frameworks\Enums\FrameworkVersionStatus;
use Trustbird\Frameworks\Events\FrameworkVersionPublished;

final readonly class PublishFrameworkVersion
{
    /**
     * @param array{
     *     published_at?: \DateTimeInterface|null,
     *     published_by_id?: string|null,
     * } $attributes
     */
    public function handle(HasFrameworks $framework, HasFrameworkVersions $version, array $attributes = []): HasFrameworkVersions
    {
        if ($version->framework_id !== $framework->id) {
            throw new InvalidArgumentException('The framework version does not belong to this framework.');
        }

        if (
            $framework->workspace_id !== null
            && isset($version->workspace_id)
            && $version->workspace_id !== null
            && $version->workspace_id !== $framework->workspace_id
        ) {
            throw new InvalidArgumentException('Related object must belong to the same workspace.');
        }

        if (! $version->canBePublished()) {
            throw new InvalidArgumentException('Only draft framework versions can be published.');
        }

        return DB::transaction(function () use ($framework, $version, $attributes): HasFrameworkVersions {
            if ($framework->current_version_id !== null) {
                /** @var HasFrameworkVersions $versionModel */
                $versionModel = app(HasFrameworkVersions::class);
                $versionModel->query()
                    ->whereKey($framework->current_version_id)
                    ->update(['status' => FrameworkVersionStatus::Superseded]);
            }

            $version->update([
                'status' => FrameworkVersionStatus::Published,
                'published_at' => $attributes['published_at'] ?? now(),
                'published_by_id' => $attributes['published_by_id'] ?? null,
            ]);

            $framework->update(['current_version_id' => $version->id]);

            FrameworkVersionPublished::dispatch($framework, $version);

            return $version->fresh();
        });
    }
}
