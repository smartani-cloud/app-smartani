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
        Schema::create('trx_daily_irrigation', function (Blueprint $table) {
            $table->id();            
            $table->unsignedBigInteger('planting_cycle_id');
			$table->decimal('temperature', 5, 2);
			$table->decimal('humidity', 5, 2);
			$table->integer('lux');
            $table->timestamp('recorded_at');
			$table->tinyInteger('irrigation_frequency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_daily_irrigation');
    }
};
