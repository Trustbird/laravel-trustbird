<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('severity')->default('medium');
            $table->string('status')->default('open');

            $table->foreignUlid('owner_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->foreignUlid('responder_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->timestamp('detected_at')->nullable();
            $table->timestamp('contained_at')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['severity', 'status']);
            $table->index('owner_id');
            $table->index('responder_id');
        });

        Schema::create('incident_notes', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->foreignUlid('incident_id')
                ->constrained('incidents')
                ->cascadeOnDelete();

            $table->foreignUlid('author_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->timestamp('occurred_at')->nullable();
            $table->text('body');

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['incident_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_notes');
        Schema::dropIfExists('incidents');
    }
};

