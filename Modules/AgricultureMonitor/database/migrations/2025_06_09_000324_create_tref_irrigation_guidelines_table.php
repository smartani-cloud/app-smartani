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
        Schema::create('tref_irrigation_guidelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plant_id');
			$table->decimal('temperature_low', 5, 2);
			$table->decimal('temperature_high', 5, 2);
			$table->decimal('humidity_low', 5, 2);
			$table->decimal('humidity_high', 5, 2);
			$table->integer('lux_low');
			$table->integer('lux_high');
			$table->tinyInteger('irrigation_frequency');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tref_irrigation_guidelines');
    }
};
