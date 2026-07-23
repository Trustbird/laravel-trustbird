<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidence', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('type')->default('other');
            $table->string('status')->default('draft');

            $table->foreignUlid('owner_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->foreignUlid('reviewer_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('next_review_at')->nullable();

            $table->string('external_url')->nullable();
            $table->string('storage_key')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('owner_id');
            $table->index('next_review_at');
        });

        Schema::create('evidence_relations', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->foreignUlid('evidence_id')
                ->constrained('evidence')
                ->cascadeOnDelete();

            $table->string('related_type');
            $table->ulid('related_id');

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(['evidence_id', 'related_type', 'related_id'], 'evidence_relations_unique');
            $table->index(['related_type', 'related_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidence_relations');
        Schema::dropIfExists('evidence');
    }
};
