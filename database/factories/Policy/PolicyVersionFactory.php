<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Policy;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Policies\Enums\PolicyVersionStatus;
use Trustbird\Policies\Models\Policy;
use Trustbird\Policies\Models\PolicyVersion;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<PolicyVersion>
 */
final class PolicyVersionFactory extends Factory
{
    protected $model = PolicyVersion::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'policy_id' => Policy::factory(),
            'version_number' => 1,
            'status' => PolicyVersionStatus::Draft,
            'content' => $this->faker->paragraphs(3, true),
            'change_summary' => $this->faker->sentence(),
            'metadata' => [],
        ];
    }

    public function draft(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => PolicyVersionStatus::Draft,
            'published_at' => null,
            'published_by_id' => null,
        ]);
    }

    public function published(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => PolicyVersionStatus::Published,
            'published_at' => now(),
        ]);
    }

    public function superseded(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => PolicyVersionStatus::Superseded,
            'published_at' => now()->subMonth(),
        ]);
    }
}
