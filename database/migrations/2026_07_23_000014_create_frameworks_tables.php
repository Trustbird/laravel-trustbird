<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('frameworks', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->string('name');
            $table->text('description')->nullable();

            $table->string('slug')->nullable();

            $table->foreignUlid('owner_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index('owner_id');
            $table->index('slug');
        });

        Schema::create('framework_versions', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->foreignUlid('framework_id')
                ->constrained('frameworks')
                ->cascadeOnDelete();

            $table->string('version_label');
            $table->string('status')->default('draft');
            $table->text('change_summary')->nullable();

            $table->timestamp('published_at')->nullable();

            $table->foreignUlid('published_by_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(['framework_id', 'version_label']);
            $table->index(['framework_id', 'status']);
        });

        Schema::table('frameworks', function (Blueprint $table): void {
            $table->foreignUlid('current_version_id')
                ->after('owner_id')
                ->nullable()
                ->constrained('framework_versions')
                ->nullOnDelete();
        });

        Schema::create('framework_requirements', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->foreignUlid('framework_version_id')
                ->constrained('framework_versions')
                ->cascadeOnDelete();

            $table->string('code')->nullable();
            $table->string('title');
            $table->text('summary')->nullable();

            $table->unsignedInteger('position')->default(0);

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['framework_version_id', 'position']);
            $table->index(['framework_version_id', 'code']);
        });

        Schema::create('framework_mappings', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->foreignUlid('requirement_id')
                ->constrained('framework_requirements')
                ->cascadeOnDelete();

            $table->string('related_type');
            $table->ulid('related_id');

            $table->string('coverage')->default('planned');

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(['requirement_id', 'related_type', 'related_id'], 'framework_mappings_unique');
            $table->index(['related_type', 'related_id']);
            $table->index('coverage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('framework_mappings');
        Schema::dropIfExists('framework_requirements');

        Schema::table('frameworks', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('current_version_id');
        });

        Schema::dropIfExists('framework_versions');
        Schema::dropIfExists('frameworks');
    }
};
