<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Interview;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Interviews\Enums\InterviewQuestionType;
use Trustbird\Interviews\Enums\InterviewSuggestionDomain;
use Trustbird\Interviews\Models\Interview;
use Trustbird\Interviews\Models\InterviewQuestion;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<InterviewQuestion>
 */
final class InterviewQuestionFactory extends Factory
{
    protected $model = InterviewQuestion::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'interview_id' => Interview::factory(),
            'position' => 0,
            'prompt' => $this->faker->sentence(8).'?',
            'help_text' => $this->faker->optional()->sentence(),
            'type' => InterviewQuestionType::Text,
            'options' => null,
            'suggestion_domain' => $this->faker->optional()->randomElement(InterviewSuggestionDomain::cases()),
            'suggestion_key' => $this->faker->optional()->slug(),
            'is_required' => true,
            'metadata' => [],
        ];
    }
}
