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
        Schema::create('trx_harvest_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('planting_cycle_id');			
			$table->date('date');
			$table->unsignedTinyInteger('harvest_quality_id');
			$table->decimal('weight_kg', 8, 2); // Total berat panen dalam kg			
			$table->unsignedTinyInteger('harvest_category_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_harvest_summaries');
    }
};
