<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->string('name');
            $table->text('description')->nullable();

            $table->string('kind');

            $table->foreignUlid('owner_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->string('provider_name')->nullable();
            $table->string('external_reference')->nullable();
            $table->string('environment')->nullable();

            $table->string('criticality')->default('normal');

            $table->boolean('contains_personal_data')->default(false);
            $table->boolean('contains_sensitive_data')->default(false);

            $table->string('status')->default('active');

            $table->timestamp('acquired_at')->nullable();
            $table->timestamp('retired_at')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['kind', 'status']);
            $table->index(['environment', 'criticality']);
            $table->index('owner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};