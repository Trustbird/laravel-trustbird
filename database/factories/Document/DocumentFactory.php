<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Document;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Documents\Enums\DocumentVersionStatus;
use Trustbird\Documents\Models\Document;
use Trustbird\Documents\Models\DocumentVersion;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<Document>
 */
final class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'owner_id' => Person::factory(),
            'reviewer_id' => Person::factory(),
            'metadata' => [],
        ];
    }

    public function withDraftVersion(array $versionAttributes = []): self
    {
        return $this->afterCreating(function (Document $document) use ($versionAttributes): void {
            DocumentVersion::factory()->create(array_merge([
                'workspace_id' => $document->workspace_id,
                'document_id' => $document->id,
                'version_number' => 1,
            ], $versionAttributes));
        });
    }

    public function withPublishedVersion(): self
    {
        return $this->afterCreating(function (Document $document): void {
            $version = DocumentVersion::factory()->create([
                'workspace_id' => $document->workspace_id,
                'document_id' => $document->id,
                'version_number' => 1,
            ]);

            $version->update([
                'status' => DocumentVersionStatus::Published,
                'published_at' => now(),
                'published_by_id' => $document->owner_id,
            ]);

            $document->update(['current_version_id' => $version->id]);
        });
    }

    public function dueForReview(): self
    {
        return $this->state(fn () => [
            'next_review_at' => now()->subDay(),
        ]);
    }
}
