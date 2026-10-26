<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('available_jobs', function (Blueprint $table) {
            $table->index(['is_active', 'deleted_at', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('available_jobs', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'deleted_at', 'published_at']);
        });
    }
};
