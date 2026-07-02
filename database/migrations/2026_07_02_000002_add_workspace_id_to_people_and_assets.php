<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table): void {
            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();
        });

        Schema::table('assets', function (Blueprint $table): void {
            $table->foreignUlid('workspace_id')->after('id')->nullable()->constrained('workspaces')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('workspace_id');
        });

        Schema::table('people', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('workspace_id');
        });
    }
};
