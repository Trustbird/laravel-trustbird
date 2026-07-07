<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->string('name');
            $table->text('description')->nullable();

            $table->string('status')->default('active');
            $table->string('criticality')->default('medium');

            $table->foreignUlid('owner_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('next_review_at')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['status', 'criticality']);
            $table->index('owner_id');
            $table->index('next_review_at');
        });

        Schema::create('supplier_relations', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->foreignUlid('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnDelete();

            $table->string('related_type');
            $table->ulid('related_id');

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(['supplier_id', 'related_type', 'related_id'], 'supplier_relations_unique');
            $table->index(['related_type', 'related_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_relations');
        Schema::dropIfExists('suppliers');
    }
};

