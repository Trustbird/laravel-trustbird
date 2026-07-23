<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Review;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\People\Models\Person;
use Trustbird\Reviews\Enums\ReviewerRole;
use Trustbird\Reviews\Models\Review;
use Trustbird\Reviews\Models\ReviewReviewer;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<ReviewReviewer>
 */
final class ReviewReviewerFactory extends Factory
{
    protected $model = ReviewReviewer::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'review_id' => Review::factory(),
            'person_id' => Person::factory(),
            'role' => ReviewerRole::Primary,
            'metadata' => [],
        ];
    }
}
