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
        Schema::create('evaluation_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained();
            $table->foreignId('employee_id')->constrained();
            $table->text('observation');
            $table->foreignId('author_id')->constrained('users', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_observations');
    }
};
