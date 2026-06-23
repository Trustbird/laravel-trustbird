<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();

            $table->string('employment_type');
            $table->string('employment_status');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('last_reminded_at')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};