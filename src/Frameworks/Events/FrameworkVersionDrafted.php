<?php

declare(strict_types=1);

namespace Trustbird\Frameworks\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trustbird\Frameworks\Contracts\HasFrameworks;
use Trustbird\Frameworks\Contracts\HasFrameworkVersions;

final class FrameworkVersionDrafted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public HasFrameworks $framework,
        public HasFrameworkVersions $version,
    ) {}
}
