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
        Schema::create('tref_plant_growth_predictions', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('plant_id');
			$table->integer('day_number');
			$table->decimal('expected_height_cm', 5, 2)->default(0);
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tref_plant_growth_predictions');
    }
};
