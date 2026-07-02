<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Risk;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\People\Models\Person;
use Trustbird\Risks\Enums\RiskLevel;
use Trustbird\Risks\Enums\RiskStatus;
use Trustbird\Risks\Enums\RiskTreatment;
use Trustbird\Risks\Models\Risk;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<Risk>
 */
final class RiskFactory extends Factory
{
    protected $model = Risk::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'owner_id' => Person::factory(),
            'status' => RiskStatus::Open,
            'treatment' => $this->faker->randomElement(RiskTreatment::cases()),
            'likelihood' => $this->faker->randomElement(RiskLevel::cases()),
            'impact' => $this->faker->randomElement(RiskLevel::cases()),
            'metadata' => [],
        ];
    }

    public function open(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => RiskStatus::Open,
        ]);
    }

    public function underReview(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => RiskStatus::UnderReview,
        ]);
    }

    public function resolved(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => RiskStatus::Resolved,
            'reviewed_at' => now(),
        ]);
    }

    public function dueForReview(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => RiskStatus::BeingAddressed,
            'next_review_at' => now()->subDay(),
        ]);
    }
}
