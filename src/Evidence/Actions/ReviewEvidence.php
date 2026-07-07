<?php

declare(strict_types=1);

namespace Trustbird\Evidence\Actions;

use Trustbird\Evidence\Contracts\HasEvidence;
use Trustbird\Evidence\Enums\EvidenceStatus;
use Trustbird\Evidence\Events\EvidenceReviewed;

final readonly class ReviewEvidence
{
    /**
     * @param array{
     *     reviewed_at?: \DateTimeInterface|null,
     *     next_review_at?: \DateTimeInterface|null,
     *     reviewer_id?: string|null,
     *     status?: string|EvidenceStatus|null,
     * } $attributes
     */
    public function handle(HasEvidence $evidence, array $attributes = []): HasEvidence
    {
        $evidence->update([
            ...$attributes,
            'reviewed_at' => $attributes['reviewed_at'] ?? now(),
        ]);

        EvidenceReviewed::dispatch($evidence);

        return $evidence;
    }
}
