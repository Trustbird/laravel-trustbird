<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Managers;

use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Trustbird\Interviews\Actions\CompleteInterview;
use Trustbird\Interviews\Contracts\HasInterviewAnswers;
use Trustbird\Interviews\Contracts\HasInterviewQuestions;
use Trustbird\Interviews\Contracts\HasInterviews;
use Trustbird\Interviews\Enums\InterviewQuestionType;
use Trustbird\Interviews\Enums\InterviewStatus;
use Trustbird\Interviews\Enums\InterviewSuggestionDomain;
use Trustbird\Interviews\Models\Interview;
use Trustbird\Interviews\Models\InterviewQuestion;

final readonly class InterviewsManager
{
    public function create(
        string $title,
        ?string $description = null,
        InterviewStatus $status = InterviewStatus::Draft,
        ?string $ownerId = null,
        array $metadata = [],
        ?string $workspaceId = null,
    ): HasInterviews {
        /** @var HasInterviews $model */
        $model = app(HasInterviews::class);

        return $model->query()->create([
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'owner_id' => $ownerId,
            'metadata' => $metadata,
            'workspace_id' => $workspaceId,
        ]);
    }

    public function update(
        HasInterviews $interview,
        ?string $title = null,
        ?string $description = null,
        ?InterviewStatus $status = null,
        ?string $ownerId = null,
        ?array $metadata = null,
    ): HasInterviews {
        $attributes = array_filter([
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'owner_id' => $ownerId,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $interview->update($attributes);

        return $interview;
    }

    public function addQuestion(
        Interview $interview,
        string $prompt,
        InterviewQuestionType $type = InterviewQuestionType::Text,
        ?string $helpText = null,
        ?array $options = null,
        ?InterviewSuggestionDomain $suggestionDomain = null,
        ?string $suggestionKey = null,
        bool $isRequired = true,
        int $position = 0,
        array $metadata = [],
    ): HasInterviewQuestions {
        return DB::transaction(function () use ($interview, $prompt, $type, $helpText, $options, $suggestionDomain, $suggestionKey, $isRequired, $position, $metadata) {
            /** @var HasInterviewQuestions $model */
            $model = app(HasInterviewQuestions::class);

            $question = $model->query()->create([
                'workspace_id' => $interview->workspace_id,
                'interview_id' => $interview->id,
                'prompt' => $prompt,
                'type' => $type,
                'help_text' => $helpText,
                'options' => $options,
                'suggestion_domain' => $suggestionDomain,
                'suggestion_key' => $suggestionKey,
                'is_required' => $isRequired,
                'position' => $position,
                'metadata' => $metadata,
            ]);

            $this->refreshProgress($interview);

            return $question;
        });
    }

    public function updateQuestion(
        HasInterviewQuestions $question,
        ?string $prompt = null,
        ?InterviewQuestionType $type = null,
        ?string $helpText = null,
        ?array $options = null,
        ?InterviewSuggestionDomain $suggestionDomain = null,
        ?string $suggestionKey = null,
        ?bool $isRequired = null,
        ?int $position = null,
        ?array $metadata = null,
    ): HasInterviewQuestions {
        $attributes = array_filter([
            'prompt' => $prompt,
            'type' => $type,
            'help_text' => $helpText,
            'options' => $options,
            'suggestion_domain' => $suggestionDomain,
            'suggestion_key' => $suggestionKey,
            'is_required' => $isRequired,
            'position' => $position,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $question->update($attributes);

        return $question;
    }

    public function answer(
        Interview $interview,
        InterviewQuestion $question,
        mixed $value,
        ?string $answeredById = null,
        ?string $notes = null,
        ?DateTimeInterface $answeredAt = null,
        array $metadata = [],
    ): HasInterviewAnswers {
        if ($question->interview_id !== $interview->id) {
            throw new InvalidArgumentException('The question does not belong to this interview.');
        }

        return DB::transaction(function () use ($interview, $question, $value, $answeredById, $notes, $answeredAt, $metadata) {
            /** @var HasInterviewAnswers $model */
            $model = app(HasInterviewAnswers::class);

            $answer = $model->query()->updateOrCreate(
                [
                    'interview_id' => $interview->id,
                    'question_id' => $question->id,
                ],
                [
                    'workspace_id' => $interview->workspace_id,
                    'answered_by_id' => $answeredById,
                    'value' => $this->normalizeValue($value),
                    'notes' => $notes,
                    'answered_at' => $answeredAt ?? now(),
                    'metadata' => $metadata,
                ],
            );

            if ($interview->status === InterviewStatus::Draft) {
                $interview->update([
                    'status' => InterviewStatus::InProgress,
                    'started_at' => $interview->started_at ?? now(),
                ]);
            }

            $this->refreshProgress($interview->fresh());

            return $answer;
        });
    }

    public function complete(HasInterviews $interview): HasInterviews
    {
        return app(CompleteInterview::class)->handle($interview);
    }

    private function refreshProgress(HasInterviews $interview): void
    {
        $interview->update([
            'question_count' => $interview->questions()->count(),
            'answered_count' => $interview->answers()->count(),
        ]);
    }

    /**
     * @return array{value: mixed}
     */
    private function normalizeValue(mixed $value): array
    {
        if (is_array($value) && array_key_exists('value', $value)) {
            return $value;
        }

        return ['value' => $value];
    }
}
