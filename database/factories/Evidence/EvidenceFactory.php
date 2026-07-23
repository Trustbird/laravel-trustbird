<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Evidence;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Evidence\Enums\EvidenceStatus;
use Trustbird\Evidence\Enums\EvidenceType;
use Trustbird\Evidence\Models\Evidence;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<Evidence>
 */
final class EvidenceFactory extends Factory
{
    protected $model = Evidence::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'type' => $this->faker->randomElement(EvidenceType::cases()),
            'status' => EvidenceStatus::Draft,
            'owner_id' => Person::factory(),
            'reviewer_id' => Person::factory(),
            'reviewed_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'next_review_at' => $this->faker->optional()->dateTimeBetween('now', '+1 year'),
            'external_url' => $this->faker->optional()->url(),
            'storage_key' => $this->faker->optional()->uuid(),
            'metadata' => ['sensitivity' => 'internal'],
        ];
    }

    public function active(): self
    {
        return $this->state(fn () => ['status' => EvidenceStatus::Active]);
    }
}
