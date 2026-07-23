<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('status')->default('draft');

            $table->foreignUlid('owner_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->unsignedInteger('answered_count')->default(0);
            $table->unsignedInteger('question_count')->default(0);

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('owner_id');
        });

        Schema::create('interview_questions', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->foreignUlid('interview_id')
                ->constrained('interviews')
                ->cascadeOnDelete();

            $table->unsignedInteger('position')->default(0);

            $table->string('prompt');
            $table->text('help_text')->nullable();

            $table->string('type')->default('text');
            $table->json('options')->nullable();

            $table->string('suggestion_domain')->nullable();
            $table->string('suggestion_key')->nullable();

            $table->boolean('is_required')->default(true);

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['interview_id', 'position']);
            $table->index('suggestion_domain');
        });

        Schema::create('interview_answers', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->foreignUlid('interview_id')
                ->constrained('interviews')
                ->cascadeOnDelete();

            $table->foreignUlid('question_id')
                ->constrained('interview_questions')
                ->cascadeOnDelete();

            $table->foreignUlid('answered_by_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->json('value')->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('answered_at')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(['interview_id', 'question_id'], 'interview_answers_unique');
            $table->index('answered_by_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_answers');
        Schema::dropIfExists('interview_questions');
        Schema::dropIfExists('interviews');
    }
};
