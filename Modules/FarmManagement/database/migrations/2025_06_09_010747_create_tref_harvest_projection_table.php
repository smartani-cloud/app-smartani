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
        Schema::create('tref_harvest_projection', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('planting_cycle_id');
			$table->decimal('min_price_per_ounce', 8, 2);
			$table->decimal('max_price_per_ounce', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tref_harvest_projection');
    }
};
