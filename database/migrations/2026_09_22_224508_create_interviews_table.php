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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained();
            $table->foreignId('procedure_stage_id')->constrained();
            $table->foreignId('scheduled_by')->constrained('users', 'id');
            $table->timestamp('scheduled_at');
            $table->integer('duration_minutes');
            $table->string('location')->nullable();
            $table->string('meeting_url')->nullable();
            $table->text('notes');
            $table->timestamps();
        });

        Schema::create('interview_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('role_id')->constrained();
            $table->timestamps();

            $table->unique(['interview_id', 'user_id']);
        });

        Schema::create('interview_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained();
            $table->string('result', 12);
            $table->integer('score');
            $table->text('comments');
            $table->foreignId('recorded_by')->constrained('users', 'id');
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        Schema::create('interview_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->unique()->constrained();
            $table->foreignId('author_id')->constrained('users', 'id');
            $table->text('observation');
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
