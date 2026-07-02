<?php

declare(strict_types=1);

namespace Trustbird\Policies\Actions;

use Illuminate\Support\Facades\DB;
use Trustbird\Policies\Enums\PolicyVersionStatus;
use Trustbird\Policies\Events\PolicyCreated;
use Trustbird\Policies\Models\Policy;
use Trustbird\Policies\Models\PolicyVersion;

final readonly class CreatePolicy
{
    /**
     * @param array{
     *     title: string,
     *     content: string,
     *     owner_id?: string|null,
     *     reviewer_id?: string|null,
     *     next_review_at?: \DateTimeInterface|null,
     *     change_summary?: string|null,
     *     metadata?: array|null,
     * } $attributes
     */
    public function handle(array $attributes): Policy
    {
        return DB::transaction(function () use ($attributes): Policy {
            $policy = Policy::query()->create([
                'title' => $attributes['title'],
                'owner_id' => $attributes['owner_id'] ?? null,
                'reviewer_id' => $attributes['reviewer_id'] ?? null,
                'next_review_at' => $attributes['next_review_at'] ?? null,
                'metadata' => $attributes['metadata'] ?? null,
            ]);

            PolicyVersion::query()->create([
                'workspace_id' => $policy->workspace_id,
                'policy_id' => $policy->id,
                'version_number' => 1,
                'status' => PolicyVersionStatus::Draft,
                'content' => $attributes['content'],
                'change_summary' => $attributes['change_summary'] ?? null,
            ]);

            PolicyCreated::dispatch($policy);

            return $policy->load('versions');
        });
    }
}
