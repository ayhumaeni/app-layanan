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
        Schema::create('service_request_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained('service_requests')->cascadeOnDelete();
            $table->foreignId('service_requirement_id')->constrained('service_requirements')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('verification_status')->default('pending'); // pending, valid, revision_needed
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('service_request_id');
            $table->index('verification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_request_documents');
    }
};
