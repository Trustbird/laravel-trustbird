<?php

declare(strict_types=1);

namespace Trustbird\Policies\Actions;

use Trustbird\Policies\Events\PolicyUpdated;
use Trustbird\Policies\Models\Policy;

final readonly class UpdatePolicy
{
    public function handle(Policy $policy, array $attributes): Policy
    {
        $policy->update($attributes);

        PolicyUpdated::dispatch($policy);

        return $policy;
    }
}
