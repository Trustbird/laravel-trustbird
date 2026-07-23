<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_providers', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->string('name');
            $table->string('driver');
            $table->boolean('is_active')->default(true);

            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['driver', 'is_active']);
        });

        Schema::create('ai_prompts', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->string('key');
            $table->string('name');
            $table->text('body');
            $table->string('purpose')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(['workspace_id', 'key']);
            $table->index('purpose');
        });

        Schema::create('ai_suggestions', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->foreignUlid('provider_id')
                ->nullable()
                ->constrained('ai_providers')
                ->nullOnDelete();

            $table->foreignUlid('prompt_id')
                ->nullable()
                ->constrained('ai_prompts')
                ->nullOnDelete();

            $table->string('kind')->default('general');
            $table->string('status')->default('pending');

            $table->string('subject_type')->nullable();
            $table->ulid('subject_id')->nullable();

            $table->string('title')->nullable();
            $table->json('input')->nullable();
            $table->json('output')->nullable();

            $table->string('model_name')->nullable();
            $table->string('provider_reference')->nullable();

            $table->foreignUlid('created_by_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->foreignUlid('reviewed_by_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['status', 'kind']);
            $table->index(['subject_type', 'subject_id']);
            $table->index('provider_id');
            $table->index('prompt_id');
        });

        Schema::create('ai_suggestion_logs', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->foreignUlid('suggestion_id')
                ->constrained('ai_suggestions')
                ->cascadeOnDelete();

            $table->string('event');

            $table->foreignUlid('actor_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->json('payload')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['suggestion_id', 'event']);
            $table->index('actor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_suggestion_logs');
        Schema::dropIfExists('ai_suggestions');
        Schema::dropIfExists('ai_prompts');
        Schema::dropIfExists('ai_providers');
    }
};
