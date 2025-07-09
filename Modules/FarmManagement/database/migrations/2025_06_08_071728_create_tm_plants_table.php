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
        Schema::create('tm_plants', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('type_id');
            $table->string('name');
			$table->string('scientific_name')->nullable();
			$table->integer('growth_cycle_days'); // Lama siklus tanam dalam hari
			$table->integer('yield_per_hole_min')->default(1); // Hasil panen minimum per lubang tanam
			$table->integer('yield_per_hole_max')->default(0); // Hasil panen maksimum per lubang tanam
			$table->decimal('fruit_weight_min_g', 8, 2)->default(0); // Berat minimum panen per buah (gram)
			$table->decimal('fruit_weight_max_g', 8, 2)->default(0); // Berat maksimum panen per buah (gram)
			$table->tinyInteger('daily_watering_min')->default(0); // Jumlah penyiraman terendah per hari
			$table->tinyInteger('daily_watering_max')->default(0); // Jumlah penyiraman tertinggi per hari			
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tm_plants');
    }
};
