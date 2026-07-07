<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('status')->default('open');
            $table->string('priority')->default('normal');

            $table->foreignUlid('owner_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->foreignUlid('assignee_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index('owner_id');
            $table->index('assignee_id');
        });

        Schema::create('task_relations', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->foreignUlid('task_id')
                ->constrained('tasks')
                ->cascadeOnDelete();

            $table->string('related_type');
            $table->ulid('related_id');

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(['task_id', 'related_type', 'related_id'], 'task_relations_unique');
            $table->index(['related_type', 'related_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_relations');
        Schema::dropIfExists('tasks');
    }
};

