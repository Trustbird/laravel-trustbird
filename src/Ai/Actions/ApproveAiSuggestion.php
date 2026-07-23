<?php

declare(strict_types=1);

namespace Trustbird\Ai\Actions;

use InvalidArgumentException;
use Trustbird\Ai\Contracts\HasAiSuggestionLogs;
use Trustbird\Ai\Contracts\HasAiSuggestions;
use Trustbird\Ai\Enums\AiSuggestionLogEvent;
use Trustbird\Ai\Enums\AiSuggestionStatus;
use Trustbird\Ai\Events\AiSuggestionApproved;

final readonly class ApproveAiSuggestion
{
    /**
     * @param array{
     *     reviewed_by_id?: string|null,
     *     reviewed_at?: \DateTimeInterface|null,
     *     review_notes?: string|null,
     * } $attributes
     */
    public function handle(HasAiSuggestions $suggestion, array $attributes = []): HasAiSuggestions
    {
        if ($suggestion->status !== AiSuggestionStatus::Pending) {
            throw new InvalidArgumentException('Only pending AI suggestions can be approved.');
        }

        $suggestion->update([
            'status' => AiSuggestionStatus::Approved,
            'reviewed_by_id' => $attributes['reviewed_by_id'] ?? null,
            'reviewed_at' => $attributes['reviewed_at'] ?? now(),
            'review_notes' => $attributes['review_notes'] ?? null,
        ]);

        /** @var HasAiSuggestionLogs $logModel */
        $logModel = app(HasAiSuggestionLogs::class);
        $logModel->query()->create([
            'workspace_id' => $suggestion->workspace_id,
            'suggestion_id' => $suggestion->id,
            'event' => AiSuggestionLogEvent::Approved,
            'actor_id' => $attributes['reviewed_by_id'] ?? null,
            'payload' => [
                'status' => AiSuggestionStatus::Approved->value,
            ],
        ]);

        AiSuggestionApproved::dispatch($suggestion);

        return $suggestion;
    }
}
