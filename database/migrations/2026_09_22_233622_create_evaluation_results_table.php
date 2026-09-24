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
        Schema::create('evaluation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_assignment_id')->constrained();
            $table->foreignId('evaluation_criteria_id')->constrained('evaluation_criteria', 'id');
            $table->float('score');
            $table->string('comment');
            $table->timestamps();

            $table->unique(['evaluation_assignment_id', 'evaluation_criteria_id'], "unique_assignment_criteria");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_results');
    }
};
