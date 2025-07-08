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
        Schema::create('tref_sensor_feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plant_id'); // Referensi ke tanaman
			$table->unsignedSmallInteger('sensor_id'); // Referensi ke jenis sensor
			$table->decimal('value', 6, 2); // Nilai sensor yang sedang diukur
			$table->decimal('min', 6, 2); // Batas minimum nilai normal
			$table->decimal('max', 6, 2); // Batas maksimum nilai aman
			$table->string('feedback'); // Umpan balik kondisi tanaman (misal: "Panas", "Ideal", "Dingin")
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tref_sensor_feedback');
    }
};
