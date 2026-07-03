<?php

declare(strict_types=1);

namespace Trustbird\Policies\Contracts;

/**
 * @property string $id
 * @property string $title
 * @property string|null $owner_id
 * @property string|null $reviewer_id
 * @property string|null $current_version_id
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property \Illuminate\Support\Carbon|null $next_review_at
 * @property array|null $metadata
 * @property string|null $workspace_id
 */
interface HasPolicies
{
}
