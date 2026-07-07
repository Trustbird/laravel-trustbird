<?php

declare(strict_types=1);

namespace Trustbird\Evidence\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trustbird\Evidence\Contracts\HasEvidence;

final class EvidenceReviewed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public HasEvidence $evidence,
    ) {}
}
