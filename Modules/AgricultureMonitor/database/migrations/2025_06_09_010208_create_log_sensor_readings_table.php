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
        Schema::create('log_sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('planting_cycle_id');
			$table->unsignedSmallInteger('sensor_id');
			$table->decimal('value', 8, 2);
			$table->timestamp('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_sensor_readings');
    }
};
