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
        Schema::create('report_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_template_id')->constrained();
            $table->foreignId('requested_by')->constrained('employees', 'id');
            $table->json('parameters');
            $table->string('status', 10);
            $table->string('file_path');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_runs');
    }
};
