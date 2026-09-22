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
        Schema::create('selection_procedures', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('description');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('procedure_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('selection_procedure_id')->constrained();
            $table->string('name', 100);
            $table->string('description');
            $table->string('stage_type', 20);
            $table->string('sort_order', 4);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained();
            $table->foreignId('applicant_profile_id')->constrained();
            $table->foreignId('current_status_id')->constrained('recruitment_statuses', 'id');
            $table->foreignId('procedure_stage_id')->constrained();
            $table->timestamp('applied_at');
            $table->timestamp('withdrawn_at');
            $table->timestamps();

            $table->unique(['job_id', 'applicant_profile_id']);
        });

        Schema::create('application_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained();
            $table->foreignId('from_status_id')->constrained('recuitment_statuses', 'id');
            $table->foreignId('to_status_id')->constrained('recuitment_statuses', 'id');
            $table->foreignId('changed_by')->constrained('users', 'id');
            $table->text('reason');
            $table->timestamps();
        });

        Schema::create('application_stage_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained();
            $table->foreignId('procedure_stage_id')->constrained();
            $table->string('status', 10);
            $table->timestamp('started_at');
            $table->timestamp('completed_at');
            $table->foreignId('completed_by')->constrained('users', 'id');
            $table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
