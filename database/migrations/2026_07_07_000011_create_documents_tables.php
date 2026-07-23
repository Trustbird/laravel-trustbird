<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

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

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['owner_id', 'next_review_at']);
            $table->index('reviewer_id');
        });

        Schema::create('document_versions', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->foreignUlid('document_id')
                ->constrained('documents')
                ->cascadeOnDelete();

            $table->unsignedInteger('version_number');

            $table->string('status')->default('draft');
            $table->text('content');
            $table->text('change_summary')->nullable();

            $table->timestamp('published_at')->nullable();

            $table->foreignUlid('published_by_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(['document_id', 'version_number']);
            $table->index(['document_id', 'status']);
        });

        Schema::table('documents', function (Blueprint $table): void {
            $table->foreignUlid('current_version_id')
                ->after('reviewer_id')
                ->nullable()
                ->constrained('document_versions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('current_version_id');
        });

        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');
    }
};
