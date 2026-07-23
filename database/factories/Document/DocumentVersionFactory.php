<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Document;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Documents\Enums\DocumentVersionStatus;
use Trustbird\Documents\Models\Document;
use Trustbird\Documents\Models\DocumentVersion;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<DocumentVersion>
 */
final class DocumentVersionFactory extends Factory
{
    protected $model = DocumentVersion::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'document_id' => Document::factory(),
            'version_number' => 1,
            'status' => DocumentVersionStatus::Draft,
            'content' => $this->faker->paragraphs(3, true),
            'change_summary' => $this->faker->optional()->sentence(),
            'metadata' => [],
        ];
    }
}
