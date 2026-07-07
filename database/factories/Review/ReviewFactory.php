<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Review;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Controls\Models\Control;
use Trustbird\People\Models\Person;
use Trustbird\Reviews\Enums\ReviewStatus;
use Trustbird\Reviews\Models\Review;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<Review>
 */
final class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'status' => ReviewStatus::Scheduled,
            'due_at' => $this->faker->optional()->dateTimeBetween('now', '+3 months'),
            'reviewer_id' => Person::factory(),
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Review $review): void {
            if ($review->reviewable_id) {
                return;
            }

            $workspaceId = $review->workspace_id;
            if ($workspaceId instanceof Workspace) {
                $workspaceId = $workspaceId->id;
            }

            $control = Control::factory()->create(['workspace_id' => $workspaceId]);

            $review->reviewable_type = Control::class;
            $review->reviewable_id = $control->id;
        });
    }

    public function completed(): self
    {
        return $this->state(fn () => [
            'status' => ReviewStatus::Completed,
            'completed_at' => now(),
        ]);
    }

    public function overdue(): self
    {
        return $this->state(fn () => [
            'status' => ReviewStatus::Scheduled,
            'due_at' => now()->subDay(),
        ]);
    }
}
