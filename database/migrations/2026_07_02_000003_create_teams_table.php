<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();

            $table->string('name');
            $table->text('description')->nullable();

            $table->foreignUlid('owner_id')->nullable()->constrained('people')->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('team_person', function (Blueprint $table): void {
            $table->foreignUlid('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignUlid('person_id')->constrained('people')->cascadeOnDelete();

            $table->primary(['team_id', 'person_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_person');
        Schema::dropIfExists('teams');
    }
};
