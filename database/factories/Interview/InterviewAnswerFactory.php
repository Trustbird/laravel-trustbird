<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Interview;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Interviews\Models\Interview;
use Trustbird\Interviews\Models\InterviewAnswer;
use Trustbird\Interviews\Models\InterviewQuestion;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<InterviewAnswer>
 */
final class InterviewAnswerFactory extends Factory
{
    protected $model = InterviewAnswer::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'interview_id' => Interview::factory(),
            'question_id' => InterviewQuestion::factory(),
            'answered_by_id' => Person::factory(),
            'value' => ['value' => $this->faker->sentence()],
            'notes' => $this->faker->optional()->sentence(),
            'answered_at' => now(),
            'metadata' => [],
        ];
    }
}
