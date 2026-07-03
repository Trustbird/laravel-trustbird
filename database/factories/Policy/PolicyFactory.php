<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Policy;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\People\Models\Person;
use Trustbird\Policies\Models\Policy;
use Trustbird\Policies\Models\PolicyVersion;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<Policy>
 */
final class PolicyFactory extends Factory
{
    protected $model = Policy::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'title' => $this->faker->sentence(3),
            'owner_id' => Person::factory(),
            'reviewer_id' => Person::factory(),
            'metadata' => [],
        ];
    }

    public function withDraftVersion(array $versionAttributes = []): self
    {
        return $this->afterCreating(function (Policy $policy) use ($versionAttributes): void {
            PolicyVersion::factory()->create(array_merge([
                'workspace_id' => $policy->workspace_id,
                'policy_id' => $policy->id,
                'version_number' => 1,
            ], $versionAttributes));
        });
    }

    public function withPublishedVersion(): self
    {
        return $this->afterCreating(function (Policy $policy): void {
            $version = PolicyVersion::factory()->create([
                'workspace_id' => $policy->workspace_id,
                'policy_id' => $policy->id,
                'version_number' => 1,
            ]);

            $version->update([
                'status' => \Trustbird\Policies\Enums\PolicyVersionStatus::Published,
                'published_at' => now(),
                'published_by_id' => $policy->owner_id,
            ]);

            $policy->update(['current_version_id' => $version->id]);
        });
    }

    public function dueForReview(): self
    {
        return $this->state(fn (array $attributes) => [
            'next_review_at' => now()->subDay(),
        ]);
    }
}
