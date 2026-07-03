<?php

declare(strict_types=1);

namespace Trustbird\Policies\Actions;

use InvalidArgumentException;
use Trustbird\Policies\Events\PolicyVersionUpdated;
use Trustbird\Policies\Models\PolicyVersion;

final readonly class UpdatePolicyVersion
{
    public function handle(PolicyVersion $version, array $attributes): PolicyVersion
    {
        if (! $version->canBeEdited()) {
            throw new InvalidArgumentException('Only draft policy versions can be updated.');
        }

        $version->update($attributes);

        PolicyVersionUpdated::dispatch($version);

        return $version;
    }
}
