<?php

declare(strict_types=1);

namespace Trustbird\Documents\Actions;

use Trustbird\Documents\Contracts\HasDocuments;
use Trustbird\Documents\Enums\DocumentVersionStatus;
use Trustbird\Documents\Events\DocumentVersionDrafted;
use Trustbird\Documents\Models\DocumentVersion;

final readonly class DraftDocumentVersion
{
    /**
     * @param array{
     *     content: string,
     *     change_summary?: string|null,
     *     metadata?: array|null,
     * } $attributes
     */
    public function handle(HasDocuments $document, array $attributes): DocumentVersion
    {
        $nextVersionNumber = (int) $document->versions()->max('version_number') + 1;

        $version = DocumentVersion::query()->create([
            'workspace_id' => $document->workspace_id,
            'document_id' => $document->id,
            'version_number' => $nextVersionNumber,
            'status' => DocumentVersionStatus::Draft,
            'content' => $attributes['content'],
            'change_summary' => $attributes['change_summary'] ?? null,
            'metadata' => $attributes['metadata'] ?? null,
        ]);

        DocumentVersionDrafted::dispatch($document, $version);

        return $version;
    }
}
