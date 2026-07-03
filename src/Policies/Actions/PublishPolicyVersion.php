<?php

declare(strict_types=1);

namespace Trustbird\Policies\Actions;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Trustbird\Policies\Enums\PolicyVersionStatus;
use Trustbird\Policies\Events\PolicyVersionPublished;
use Trustbird\Policies\Models\Policy;
use Trustbird\Policies\Models\PolicyVersion;

final readonly class PublishPolicyVersion
{
    /**
     * @param array{
     *     published_at?: \DateTimeInterface|null,
     *     published_by_id?: string|null,
     * } $attributes
     */
    public function handle(Policy $policy, PolicyVersion $version, array $attributes = []): PolicyVersion
    {
        if ($version->policy_id !== $policy->id) {
            throw new InvalidArgumentException('The policy version does not belong to this policy.');
        }

        if (! $version->canBePublished()) {
            throw new InvalidArgumentException('Only draft policy versions can be published.');
        }

        return DB::transaction(function () use ($policy, $version, $attributes): PolicyVersion {
            if ($policy->current_version_id !== null) {
                PolicyVersion::query()
                    ->whereKey($policy->current_version_id)
                    ->update(['status' => PolicyVersionStatus::Superseded]);
            }

            $version->update([
                'status' => PolicyVersionStatus::Published,
                'published_at' => $attributes['published_at'] ?? now(),
                'published_by_id' => $attributes['published_by_id'] ?? null,
            ]);

            $policy->update(['current_version_id' => $version->id]);

            PolicyVersionPublished::dispatch($policy, $version);

            return $version->fresh();
        });
    }
}
