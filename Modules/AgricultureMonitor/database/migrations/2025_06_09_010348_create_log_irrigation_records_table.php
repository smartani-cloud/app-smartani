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
        Schema::create('log_irrigation_records', function (Blueprint $table) {
            $table->id();            
            $table->unsignedBigInteger('planting_cycle_id');
			$table->timestamp('irrigation_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_irrigation_records');
    }
};
