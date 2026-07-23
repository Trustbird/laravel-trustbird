<?php

declare(strict_types=1);

namespace Trustbird\Documents\Actions;

use Trustbird\Documents\Contracts\HasDocuments;
use Trustbird\Documents\Events\DocumentReviewed;

final readonly class ReviewDocument
{
    /**
     * @param array{
     *     reviewed_at?: \DateTimeInterface|null,
     *     next_review_at?: \DateTimeInterface|null,
     *     reviewer_id?: string|null,
     * } $attributes
     */
    public function handle(HasDocuments $document, array $attributes = []): HasDocuments
    {
        $document->update([
            ...$attributes,
            'reviewed_at' => $attributes['reviewed_at'] ?? now(),
        ]);

        DocumentReviewed::dispatch($document);

        return $document;
    }
}
