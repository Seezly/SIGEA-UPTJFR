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
        Schema::create('historical_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type', 100);
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->json('snapshot');
            $table->foreignId('generated_by')->constrained('users', 'id');
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historical_reports');
    }
};
