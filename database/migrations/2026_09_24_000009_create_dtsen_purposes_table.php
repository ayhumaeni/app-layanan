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
        Schema::create('dtsen_purposes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // spmb, pip, kip_kuliah, bansos, kesehatan, lainnya
            $table->string('name');
            $table->unsignedTinyInteger('max_decile');
            $table->unsignedInteger('validity_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dtsen_purposes');
    }
};
