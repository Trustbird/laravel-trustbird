<?php

declare(strict_types=1);

namespace Trustbird\Documents\Actions;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Trustbird\Documents\Contracts\HasDocuments;
use Trustbird\Documents\Enums\DocumentVersionStatus;
use Trustbird\Documents\Events\DocumentVersionPublished;
use Trustbird\Documents\Models\DocumentVersion;

final readonly class PublishDocumentVersion
{
    /**
     * @param array{
     *     published_at?: \DateTimeInterface|null,
     *     published_by_id?: string|null,
     * } $attributes
     */
    public function handle(HasDocuments $document, DocumentVersion $version, array $attributes = []): DocumentVersion
    {
        if ($version->document_id !== $document->id) {
            throw new InvalidArgumentException('The document version does not belong to this document.');
        }

        if (! $version->canBePublished()) {
            throw new InvalidArgumentException('Only draft document versions can be published.');
        }

        return DB::transaction(function () use ($document, $version, $attributes): DocumentVersion {
            if ($document->current_version_id !== null) {
                DocumentVersion::query()
                    ->whereKey($document->current_version_id)
                    ->update(['status' => DocumentVersionStatus::Superseded]);
            }

            $version->update([
                'status' => DocumentVersionStatus::Published,
                'published_at' => $attributes['published_at'] ?? now(),
                'published_by_id' => $attributes['published_by_id'] ?? null,
            ]);

            $document->update(['current_version_id' => $version->id]);

            DocumentVersionPublished::dispatch($document, $version);

            return $version->fresh();
        });
    }
}
