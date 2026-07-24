<?php

declare(strict_types=1);

namespace Trustbird\Ai\Managers;

use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Trustbird\Ai\Actions\ApproveAiSuggestion;
use Trustbird\Ai\Actions\RejectAiSuggestion;
use Trustbird\Ai\Actions\WithdrawAiSuggestion;
use Trustbird\Ai\Contracts\HasAiPrompts;
use Trustbird\Ai\Contracts\HasAiProviders;
use Trustbird\Ai\Contracts\HasAiSuggestionLogs;
use Trustbird\Ai\Contracts\HasAiSuggestions;
use Trustbird\Ai\Enums\AiProviderDriver;
use Trustbird\Ai\Enums\AiSuggestionKind;
use Trustbird\Ai\Enums\AiSuggestionLogEvent;
use Trustbird\Ai\Enums\AiSuggestionStatus;

final readonly class AiManager
{
    public function registerProvider(
        string $name,
        AiProviderDriver $driver = AiProviderDriver::Custom,
        bool $isActive = true,
        array $settings = [],
        array $metadata = [],
        ?string $workspaceId = null,
    ): HasAiProviders {
        /** @var HasAiProviders $model */
        $model = app(HasAiProviders::class);

        return $model->query()->create([
            'name' => $name,
            'driver' => $driver,
            'is_active' => $isActive,
            'settings' => $settings,
            'metadata' => $metadata,
            'workspace_id' => $workspaceId,
        ]);
    }

    public function updateProvider(
        HasAiProviders $provider,
        ?string $name = null,
        ?AiProviderDriver $driver = null,
        ?bool $isActive = null,
        ?array $settings = null,
        ?array $metadata = null,
    ): HasAiProviders {
        $attributes = array_filter([
            'name' => $name,
            'driver' => $driver,
            'is_active' => $isActive,
            'settings' => $settings,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $provider->update($attributes);

        return $provider;
    }

    public function createPrompt(
        string $key,
        string $name,
        string $body,
        ?string $purpose = null,
        array $metadata = [],
        ?string $workspaceId = null,
    ): HasAiPrompts {
        /** @var HasAiPrompts $model */
        $model = app(HasAiPrompts::class);

        return $model->query()->create([
            'key' => $key,
            'name' => $name,
            'body' => $body,
            'purpose' => $purpose,
            'metadata' => $metadata,
            'workspace_id' => $workspaceId,
        ]);
    }

    public function updatePrompt(
        HasAiPrompts $prompt,
        ?string $key = null,
        ?string $name = null,
        ?string $body = null,
        ?string $purpose = null,
        ?array $metadata = null,
    ): HasAiPrompts {
        $attributes = array_filter([
            'key' => $key,
            'name' => $name,
            'body' => $body,
            'purpose' => $purpose,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $prompt->update($attributes);

        return $prompt;
    }

    /**
     * Record AI output as a suggestion that still requires human approval.
     *
     * @param  array<string, mixed>  $input
     * @param  array<string, mixed>  $output
     */
    public function suggest(
        array $output,
        AiSuggestionKind $kind = AiSuggestionKind::General,
        ?HasAiProviders $provider = null,
        ?HasAiPrompts $prompt = null,
        ?object $subject = null,
        ?string $title = null,
        array $input = [],
        ?string $modelName = null,
        ?string $providerReference = null,
        ?string $createdById = null,
        array $metadata = [],
        ?string $workspaceId = null,
    ): HasAiSuggestions {
        return DB::transaction(function () use ($output, $kind, $provider, $prompt, $subject, $title, $input, $modelName, $providerReference, $createdById, $metadata, $workspaceId) {
            $resolvedWorkspaceId = $workspaceId
                ?? $provider?->workspace_id
                ?? $prompt?->workspace_id
                ?? (isset($subject->workspace_id) ? $subject->workspace_id : null);

            $this->assertSameWorkspace($resolvedWorkspaceId, $provider);
            $this->assertSameWorkspace($resolvedWorkspaceId, $prompt);
            $this->assertSameWorkspace($resolvedWorkspaceId, $subject);

            /** @var HasAiSuggestions $model */
            $model = app(HasAiSuggestions::class);

            $suggestion = $model->query()->create([
                'workspace_id' => $resolvedWorkspaceId,
                'provider_id' => $provider?->id,
                'prompt_id' => $prompt?->id,
                'kind' => $kind,
                'status' => AiSuggestionStatus::Pending,
                'subject_type' => $subject ? $subject::class : null,
                'subject_id' => $subject?->id,
                'title' => $title,
                'input' => $input,
                'output' => $output,
                'model_name' => $modelName,
                'provider_reference' => $providerReference,
                'created_by_id' => $createdById,
                'metadata' => $metadata,
            ]);

            /** @var HasAiSuggestionLogs $logModel */
            $logModel = app(HasAiSuggestionLogs::class);
            $logModel->query()->create([
                'workspace_id' => $suggestion->workspace_id,
                'suggestion_id' => $suggestion->id,
                'event' => AiSuggestionLogEvent::Created,
                'actor_id' => $createdById,
                'payload' => [
                    'status' => AiSuggestionStatus::Pending->value,
                    'kind' => $kind->value,
                ],
            ]);

            return $suggestion;
        });
    }

    public function approve(
        HasAiSuggestions $suggestion,
        ?string $reviewedById = null,
        ?DateTimeInterface $reviewedAt = null,
        ?string $reviewNotes = null,
    ): HasAiSuggestions {
        return app(ApproveAiSuggestion::class)->handle($suggestion, array_filter([
            'reviewed_by_id' => $reviewedById,
            'reviewed_at' => $reviewedAt,
            'review_notes' => $reviewNotes,
        ], fn ($value) => $value !== null));
    }

    public function reject(
        HasAiSuggestions $suggestion,
        ?string $reviewedById = null,
        ?DateTimeInterface $reviewedAt = null,
        ?string $reviewNotes = null,
    ): HasAiSuggestions {
        return app(RejectAiSuggestion::class)->handle($suggestion, array_filter([
            'reviewed_by_id' => $reviewedById,
            'reviewed_at' => $reviewedAt,
            'review_notes' => $reviewNotes,
        ], fn ($value) => $value !== null));
    }

    public function withdraw(
        HasAiSuggestions $suggestion,
        ?string $actorId = null,
        ?string $reviewNotes = null,
    ): HasAiSuggestions {
        return app(WithdrawAiSuggestion::class)->handle($suggestion, array_filter([
            'reviewed_by_id' => $actorId,
            'review_notes' => $reviewNotes,
        ], fn ($value) => $value !== null));
    }

    private function assertSameWorkspace(?string $workspaceId, ?object $related): void
    {
        if ($related === null || $workspaceId === null) {
            return;
        }

        if (! isset($related->workspace_id) || $related->workspace_id === null) {
            return;
        }

        if ($related->workspace_id !== $workspaceId) {
            throw new InvalidArgumentException('Related object must belong to the same workspace.');
        }
    }
}
