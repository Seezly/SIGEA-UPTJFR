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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('people_id')->constrained();
            $table->string('document_type', 100);
            $table->string('file_name');
            $table->string('mime_type');
            $table->string('file_path');
            $table->string('file_size');
            $table->string('verification_status', 12);
            $table->foreignId('verified_by')->nullable()->constrained('users', 'id');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('applicant_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('people_id')->unique()->constrained();
            $table->foreignId('current_curriculum_vitae_id')->nullable()->unique()->constrained('documents')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_profiles');
        Schema::dropIfExists('documents');
    }
};
