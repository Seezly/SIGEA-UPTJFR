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
        Schema::create('backup_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('backup_run_id')->constrained();
            $table->string('verification_type', 100);
            $table->string('status', 10);
            $table->json('details');
            $table->timestamp('verified_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_verifications');
    }
};
