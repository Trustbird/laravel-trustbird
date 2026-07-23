<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Actions;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Trustbird\Frameworks\Contracts\HasFrameworks;
use Trustbird\Frameworks\Enums\FrameworkVersionStatus;
use Trustbird\Frameworks\Events\FrameworkVersionPublished;
use Trustbird\Frameworks\Models\FrameworkVersion;

final readonly class PublishFrameworkVersion
{
    /**
     * @param array{
     *     published_at?: \DateTimeInterface|null,
     *     published_by_id?: string|null,
     * } $attributes
     */
    public function handle(HasFrameworks $framework, FrameworkVersion $version, array $attributes = []): FrameworkVersion
    {
        if ($version->framework_id !== $framework->id) {
            throw new InvalidArgumentException('The framework version does not belong to this framework.');
        }

        if (! $version->canBePublished()) {
            throw new InvalidArgumentException('Only draft framework versions can be published.');
        }

        return DB::transaction(function () use ($framework, $version, $attributes): FrameworkVersion {
            if ($framework->current_version_id !== null) {
                FrameworkVersion::query()
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
