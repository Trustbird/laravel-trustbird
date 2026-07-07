<?php

declare(strict_types=1);

namespace Trustbird\Controls\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trustbird\Controls\Contracts\HasControls;

final class ControlApproved
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public HasControls $control,
    ) {}
}
