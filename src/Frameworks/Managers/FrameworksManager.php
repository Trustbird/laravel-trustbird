<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Managers;

use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Trustbird\Frameworks\Actions\DraftFrameworkVersion;
use Trustbird\Frameworks\Actions\PublishFrameworkVersion;
use Trustbird\Frameworks\Contracts\HasFrameworkMappings;
use Trustbird\Frameworks\Contracts\HasFrameworkRequirements;
use Trustbird\Frameworks\Contracts\HasFrameworks;
use Trustbird\Frameworks\Enums\FrameworkMappingCoverage;
use Trustbird\Frameworks\Models\FrameworkRequirement;
use Trustbird\Frameworks\Models\FrameworkVersion;

final readonly class FrameworksManager
{
    public function create(
        string $name,
        ?string $description = null,
        ?string $slug = null,
        ?string $ownerId = null,
        ?string $versionLabel = null,
        ?string $changeSummary = null,
        array $metadata = [],
        ?string $workspaceId = null,
    ): HasFrameworks {
        /** @var HasFrameworks $model */
        $model = app(HasFrameworks::class);

        return DB::transaction(function () use ($model, $name, $description, $slug, $ownerId, $versionLabel, $changeSummary, $metadata, $workspaceId) {
            $framework = $model->query()->create([
                'name' => $name,
                'description' => $description,
                'slug' => $slug,
                'owner_id' => $ownerId,
                'metadata' => $metadata,
                'workspace_id' => $workspaceId,
            ]);

            if ($versionLabel !== null) {
                $this->draftVersion(
                    framework: $framework,
                    versionLabel: $versionLabel,
                    changeSummary: $changeSummary,
                );
            }

            return $framework;
        });
    }

    public function update(
        HasFrameworks $framework,
        ?string $name = null,
        ?string $description = null,
        ?string $slug = null,
        ?string $ownerId = null,
        ?array $metadata = null,
    ): HasFrameworks {
        $attributes = array_filter([
            'name' => $name,
            'description' => $description,
            'slug' => $slug,
            'owner_id' => $ownerId,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $framework->update($attributes);

        return $framework;
    }

    public function draftVersion(
        HasFrameworks $framework,
        string $versionLabel,
        ?string $changeSummary = null,
    ): FrameworkVersion {
        return app(DraftFrameworkVersion::class)->handle($framework, [
            'version_label' => $versionLabel,
            'change_summary' => $changeSummary,
        ]);
    }

    public function publishVersion(
        HasFrameworks $framework,
        FrameworkVersion $version,
        ?DateTimeInterface $publishedAt = null,
        ?string $publishedById = null,
    ): FrameworkVersion {
        return app(PublishFrameworkVersion::class)->handle($framework, $version, array_filter([
            'published_at' => $publishedAt,
            'published_by_id' => $publishedById,
        ], fn ($value) => $value !== null));
    }

    public function addRequirement(
        FrameworkVersion $version,
        string $title,
        ?string $code = null,
        ?string $summary = null,
        int $position = 0,
        array $metadata = [],
    ): HasFrameworkRequirements {
        /** @var HasFrameworkRequirements $model */
        $model = app(HasFrameworkRequirements::class);

        return $model->query()->create([
            'workspace_id' => $version->workspace_id,
            'framework_version_id' => $version->id,
            'code' => $code,
            'title' => $title,
            'summary' => $summary,
            'position' => $position,
            'metadata' => $metadata,
        ]);
    }

    public function updateRequirement(
        HasFrameworkRequirements $requirement,
        ?string $title = null,
        ?string $code = null,
        ?string $summary = null,
        ?int $position = null,
        ?array $metadata = null,
    ): HasFrameworkRequirements {
        $attributes = array_filter([
            'title' => $title,
            'code' => $code,
            'summary' => $summary,
            'position' => $position,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $requirement->update($attributes);

        return $requirement;
    }

    public function map(
        FrameworkRequirement $requirement,
        object $related,
        FrameworkMappingCoverage $coverage = FrameworkMappingCoverage::Planned,
        array $metadata = [],
    ): HasFrameworkMappings {
        /** @var HasFrameworkMappings $model */
        $model = app(HasFrameworkMappings::class);

        return $model->query()->create([
            'workspace_id' => $requirement->workspace_id,
            'requirement_id' => $requirement->id,
            'related_type' => $related::class,
            'related_id' => $related->id,
            'coverage' => $coverage,
            'metadata' => $metadata,
        ]);
    }
}
