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
        Schema::create('tm_greenhouses', function (Blueprint $table) {
            $table->id();			
			$table->unsignedSmallInteger('unit_id');
			$table->string('id_greenhouse', 21)->unique();
			$table->string('photo')->nullable();
            $table->tinyInteger('irrigation_system_id')->nullable();		
			$table->string('address');
			$table->tinyInteger('rt');
			$table->tinyInteger('rw');
            $table->decimal('area', 8, 2)->nullable();
			$table->decimal('elevation', 8, 2)->nullable();
            $table->decimal('gps_lat', 10, 7)->nullable();
            $table->decimal('gps_lng', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tm_greenhouses');
    }
};
