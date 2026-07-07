<?php

declare(strict_types=1);

namespace Trustbird\Documents\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trustbird\Documents\Contracts\HasDocuments;

final class DocumentReviewed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public HasDocuments $document,
    ) {}
}
