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
        Schema::create('hiring_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained();
            $table->string('decision', 20);
            $table->string('reason');
            $table->foreignId('decided_by')->constrained('users', 'id');
            $table->timestamp('decided_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hiring_decisions');
    }
};
