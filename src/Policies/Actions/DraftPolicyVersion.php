<?php

declare(strict_types=1);

namespace Trustbird\Policies\Actions;

use Trustbird\Policies\Enums\PolicyVersionStatus;
use Trustbird\Policies\Events\PolicyVersionDrafted;
use Trustbird\Policies\Models\Policy;
use Trustbird\Policies\Models\PolicyVersion;

final readonly class DraftPolicyVersion
{
    /**
     * @param array{
     *     content: string,
     *     change_summary?: string|null,
     *     metadata?: array|null,
     * } $attributes
     */
    public function handle(Policy $policy, array $attributes): PolicyVersion
    {
        $nextVersionNumber = (int) $policy->versions()->max('version_number') + 1;

        $version = PolicyVersion::query()->create([
            'workspace_id' => $policy->workspace_id,
            'policy_id' => $policy->id,
            'version_number' => $nextVersionNumber,
            'status' => PolicyVersionStatus::Draft,
            'content' => $attributes['content'],
            'change_summary' => $attributes['change_summary'] ?? null,
            'metadata' => $attributes['metadata'] ?? null,
        ]);

        PolicyVersionDrafted::dispatch($policy, $version);

        return $version;
    }
}
