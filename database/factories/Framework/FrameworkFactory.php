<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Framework;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Frameworks\Enums\FrameworkVersionStatus;
use Trustbird\Frameworks\Models\Framework;
use Trustbird\Frameworks\Models\FrameworkVersion;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<Framework>
 */
final class FrameworkFactory extends Factory
{
    protected $model = Framework::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'slug' => $this->faker->optional()->slug(),
            'owner_id' => Person::factory(),
            'metadata' => [],
        ];
    }

    public function withDraftVersion(array $versionAttributes = []): self
    {
        return $this->afterCreating(function (Framework $framework) use ($versionAttributes): void {
            FrameworkVersion::factory()->create(array_merge([
                'workspace_id' => $framework->workspace_id,
                'framework_id' => $framework->id,
                'version_label' => '1.0',
            ], $versionAttributes));
        });
    }

    public function withPublishedVersion(): self
    {
        return $this->afterCreating(function (Framework $framework): void {
            $version = FrameworkVersion::factory()->create([
                'workspace_id' => $framework->workspace_id,
                'framework_id' => $framework->id,
                'version_label' => '1.0',
            ]);

            $version->update([
                'status' => FrameworkVersionStatus::Published,
                'published_at' => now(),
                'published_by_id' => $framework->owner_id,
            ]);

            $framework->update(['current_version_id' => $version->id]);
        });
    }
}
