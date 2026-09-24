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
        Schema::create('restoration_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('backup_run_id')->constrained();
            $table->foreignId('requested_by')->constrained('users', 'id');
            $table->string('status', 10);
            $table->string('target_environment');
            $table->json('details');
            $table->timestamp('started_at');
            $table->timestamp('completed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restorations_runs');
    }
};
