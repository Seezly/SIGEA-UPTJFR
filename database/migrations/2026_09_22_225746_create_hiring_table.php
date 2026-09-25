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
            $table->foreignId('application_id')->unique()->constrained();
            $table->string('decision', 20);
            $table->string('reason');
            $table->foreignId('decided_by')->constrained('employees', 'id');
            $table->timestamp('decided_at')->useCurrent();
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
