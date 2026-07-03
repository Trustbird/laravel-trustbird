<?php

declare(strict_types=1);

namespace Trustbird\Policies\Managers;

use Trustbird\Policies\Actions\DraftPolicyVersion;
use Trustbird\Policies\Actions\PublishPolicyVersion;
use Trustbird\Policies\Actions\ReviewPolicy;
use Trustbird\Policies\Actions\UpdatePolicyVersion;
use Trustbird\Policies\Contracts\HasPolicies;
use Trustbird\Policies\Models\PolicyVersion;
use DateTimeInterface;

final readonly class PoliciesManager
{
    public function create(
        string $title,
        ?string $content = null,
        ?string $ownerId = null,
        ?string $reviewerId = null,
        ?string $changeSummary = null,
        array $metadata = [],
        ?string $workspaceId = null,
    ): HasPolicies {
        /** @var HasPolicies $model */
        $model = app(HasPolicies::class);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($model, $title, $content, $ownerId, $reviewerId, $changeSummary, $metadata, $workspaceId) {
            $policy = $model->query()->create([
                'title' => $title,
                'owner_id' => $ownerId,
                'reviewer_id' => $reviewerId,
                'metadata' => $metadata,
                'workspace_id' => $workspaceId,
            ]);

            if ($content !== null) {
                $this->draftVersion(
                    policy: $policy,
                    content: $content,
                    notes: $changeSummary,
                );
            }

            return $policy;
        });
    }

    public function update(
        HasPolicies $policy,
        ?string $title = null,
        ?string $ownerId = null,
        ?string $reviewerId = null,
        ?array $metadata = null,
    ): HasPolicies {
        $attributes = array_filter([
            'title' => $title,
            'owner_id' => $ownerId,
            'reviewer_id' => $reviewerId,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $policy->update($attributes);

        return $policy;
    }

    public function draftVersion(
        HasPolicies $policy,
        string $content,
        ?string $notes = null,
        ?string $authorId = null,
    ): PolicyVersion {
        return app(DraftPolicyVersion::class)->handle($policy, [
            'content' => $content,
            'notes' => $notes,
            'author_id' => $authorId,
        ]);
    }

    public function updateVersion(
        PolicyVersion $version,
        ?string $content = null,
        ?string $notes = null,
    ): PolicyVersion {
        $attributes = array_filter([
            'content' => $content,
            'notes' => $notes,
        ], fn ($value) => $value !== null);

        return app(UpdatePolicyVersion::class)->handle($version, $attributes);
    }

    public function publishVersion(
        HasPolicies $policy,
        PolicyVersion $version,
        ?DateTimeInterface $publishedAt = null,
        ?string $publishedById = null,
    ): PolicyVersion {
        return app(PublishPolicyVersion::class)->handle($policy, $version, array_filter([
            'published_at' => $publishedAt,
            'published_by_id' => $publishedById,
        ], fn ($value) => $value !== null));
    }

    public function review(
        HasPolicies $policy,
        ?DateTimeInterface $reviewedAt = null,
        ?DateTimeInterface $nextReviewAt = null,
        ?string $reviewerId = null,
        ?string $notes = null,
    ): HasPolicies {
        return app(ReviewPolicy::class)->handle($policy, array_filter([
            'reviewed_at' => $reviewedAt,
            'next_review_at' => $nextReviewAt,
            'reviewer_id' => $reviewerId,
            'notes' => $notes,
        ], fn ($value) => $value !== null));
    }
}
