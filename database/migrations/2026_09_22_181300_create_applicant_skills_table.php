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
        Schema::create('applicant_skills', function (Blueprint $table) {
            $table->foreignId('skill_id')->constrained();
            $table->foreignId('applicant_profile_id')->constrained();
            $table->string('level', 15);
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['skill_id', 'applicant_profile_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_skills');
    }
};
