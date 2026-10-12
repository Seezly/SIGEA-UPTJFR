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
        Schema::table('documents', function (Blueprint $table) {
            $table->string('mime_type', 100)->change();
            $table->unsignedBigInteger('file_size')->change();
        });

        Schema::table('employee_documents', function (Blueprint $table) {
            $table->string('mime_type', 100)->change();
            $table->unsignedBigInteger('file_size')->change();
            $table->foreignId('verified_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('mime_type')->change();
            $table->string('file_size')->change();
        });

        Schema::table('employee_documents', function (Blueprint $table) {
            $table->string('mime_type', 20)->change();
            $table->integer('file_size')->change();
            $table->foreignId('verified_by')->nullable(false)->change();
        });
    }
};
