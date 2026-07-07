<?php

declare(strict_types=1);

namespace Trustbird\Documents\Managers;

use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Trustbird\Documents\Actions\DraftDocumentVersion;
use Trustbird\Documents\Actions\PublishDocumentVersion;
use Trustbird\Documents\Actions\ReviewDocument;
use Trustbird\Documents\Contracts\HasDocuments;
use Trustbird\Documents\Models\DocumentVersion;

final readonly class DocumentsManager
{
    public function create(
        string $title,
        ?string $description = null,
        ?string $content = null,
        ?string $ownerId = null,
        ?string $reviewerId = null,
        ?string $changeSummary = null,
        array $metadata = [],
        ?string $workspaceId = null,
    ): HasDocuments {
        /** @var HasDocuments $model */
        $model = app(HasDocuments::class);

        return DB::transaction(function () use ($model, $title, $description, $content, $ownerId, $reviewerId, $changeSummary, $metadata, $workspaceId) {
            $document = $model->query()->create([
                'title' => $title,
                'description' => $description,
                'owner_id' => $ownerId,
                'reviewer_id' => $reviewerId,
                'metadata' => $metadata,
                'workspace_id' => $workspaceId,
            ]);

            if ($content !== null) {
                $this->draftVersion(
                    document: $document,
                    content: $content,
                    changeSummary: $changeSummary,
                );
            }

            return $document;
        });
    }

    public function update(
        HasDocuments $document,
        ?string $title = null,
        ?string $description = null,
        ?string $ownerId = null,
        ?string $reviewerId = null,
        ?array $metadata = null,
    ): HasDocuments {
        $attributes = array_filter([
            'title' => $title,
            'description' => $description,
            'owner_id' => $ownerId,
            'reviewer_id' => $reviewerId,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $document->update($attributes);

        return $document;
    }

    public function draftVersion(
        HasDocuments $document,
        string $content,
        ?string $changeSummary = null,
    ): DocumentVersion {
        return app(DraftDocumentVersion::class)->handle($document, [
            'content' => $content,
            'change_summary' => $changeSummary,
        ]);
    }

    public function publishVersion(
        HasDocuments $document,
        DocumentVersion $version,
        ?DateTimeInterface $publishedAt = null,
        ?string $publishedById = null,
    ): DocumentVersion {
        return app(PublishDocumentVersion::class)->handle($document, $version, array_filter([
            'published_at' => $publishedAt,
            'published_by_id' => $publishedById,
        ], fn ($value) => $value !== null));
    }

    public function review(
        HasDocuments $document,
        ?DateTimeInterface $reviewedAt = null,
        ?DateTimeInterface $nextReviewAt = null,
        ?string $reviewerId = null,
    ): HasDocuments {
        return app(ReviewDocument::class)->handle($document, array_filter([
            'reviewed_at' => $reviewedAt,
            'next_review_at' => $nextReviewAt,
            'reviewer_id' => $reviewerId,
        ], fn ($value) => $value !== null));
    }
}
