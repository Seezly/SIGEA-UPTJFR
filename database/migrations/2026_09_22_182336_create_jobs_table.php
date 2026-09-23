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
        Schema::create('available_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained();
            $table->foreignId('position_id')->constrained();
            $table->foreignId('job_category_id')->constrained();
            $table->foreignId('created_by')->constrained('users', 'id');
            $table->string('title');
            $table->string('description');
            $table->text('requirements');
            $table->text('responsibilities');
            $table->string('employment_type');
            $table->integer('vacancies_count');
            $table->boolean('is_active');
            $table->timestamp('published_at');
            $table->timestamp('application_deadline');
            $table->timestamp('closed_at');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('job_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('available_job_id')->constrained();
            $table->string('requirement_type', 100);
            $table->string('description');
            $table->integer('weight');
            $table->boolean('is_required')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('job_publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('available_job_id')->constrained();
            $table->string('channel');
            $table->timestamp('published_at');
            $table->timestamp('unpublished_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('job_favorites', function (Blueprint $table) {
            $table->foreignId('applicant_profile_id')->constrained();
            $table->foreignId('available_job_id')->constrained();
            $table->timestamps();

            $table->primary(['applicant_profile_id', 'available_job_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('available_jobs');
        Schema::dropIfExists('job_requirements');
        Schema::dropIfExists('job_publications');
        Schema::dropIfExists('job_favorites');
    }
};
