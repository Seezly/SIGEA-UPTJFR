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
        Schema::create('evaluation_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained();
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('evaluator_id')->constrained('employees', 'id');
            $table->string('status', 10);
            $table->timestamp('assigned_at')->useCurrent();
            $table->foreignId('assigned_by')->constrained('users', 'id');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['evaluation_id', 'employee_id', 'evaluator_id'], "unique_evaluation_employee_evaluator");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_assignments');
    }
};
