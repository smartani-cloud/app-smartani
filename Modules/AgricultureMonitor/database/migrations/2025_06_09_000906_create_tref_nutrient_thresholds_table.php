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
        Schema::create('tref_nutrient_thresholds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plant_id');
			$table->unsignedSmallInteger('unit_id');
			$table->decimal('tds_low', 6, 2); // Batas minimum sebelum nutrisi dialirkan
			$table->decimal('tds_high', 6, 2); // Batas maksimum sebelum nutrisi dihentikan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tref_nutrient_thresholds');
    }
};
