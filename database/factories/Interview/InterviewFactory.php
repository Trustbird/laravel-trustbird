<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Interview;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Interviews\Enums\InterviewStatus;
use Trustbird\Interviews\Models\Interview;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<Interview>
 */
final class InterviewFactory extends Factory
{
    protected $model = Interview::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'status' => InterviewStatus::Draft,
            'owner_id' => Person::factory(),
            'answered_count' => 0,
            'question_count' => 0,
            'metadata' => [],
        ];
    }

    public function inProgress(): self
    {
        return $this->state(fn () => [
            'status' => InterviewStatus::InProgress,
            'started_at' => now()->subHour(),
        ]);
    }

    public function completed(): self
    {
        return $this->state(fn () => [
            'status' => InterviewStatus::Completed,
            'started_at' => now()->subDay(),
            'completed_at' => now(),
        ]);
    }
}
