<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->string('reviewable_type');
            $table->ulid('reviewable_id');

            $table->string('status')->default('scheduled');

            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->foreignUlid('reviewer_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['reviewable_type', 'reviewable_id']);
            $table->index(['status', 'due_at']);
            $table->index('reviewer_id');
        });

        Schema::create('review_reviewers', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->foreignUlid('review_id')
                ->constrained('reviews')
                ->cascadeOnDelete();

            $table->foreignUlid('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            $table->string('role')->default('primary');

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(['review_id', 'person_id'], 'review_reviewers_unique');
            $table->index('person_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_reviewers');
        Schema::dropIfExists('reviews');
    }
};
