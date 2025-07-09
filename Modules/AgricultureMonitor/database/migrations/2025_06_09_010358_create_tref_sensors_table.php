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
        Schema::create('tref_sensors', function (Blueprint $table) {
            $table->smallIncrements('id');
			$table->string('name');
			$table->string('unit', 20)->nullable(); // Satuan data sensor (°C, %, Lux, dsb.)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tref_sensors');
    }
};
