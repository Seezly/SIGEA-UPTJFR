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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained();
            $table->string('employee_code', 16)->unique();
            $table->timestamp('hire_date');
            $table->string('status', 20);
            $table->foreignId('current_area_id')->constrained('areas', 'id');
            $table->foreignId('current_position_id')->constrained('positions', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->string('document_type', 100);
            $table->string('file_name');
            $table->string('file_path');
            $table->string('myme_type', 20);
            $table->integer('file_size');
            $table->string('verification_status', 12);
            $table->foreignId('verified_by')->constrained('users', 'id');
            $table->timestamp('verified_at');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('employee_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->unique()->constrained();
            $table->string('record_type', 50);
            $table->json('record_value');
            $table->string('status', 10);
            $table->foreignId('evidence_id')->constrained('employee_documents', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
