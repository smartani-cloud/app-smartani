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
        Schema::create('tas_greenhouse_owner_units', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('owner_id');
            $table->smallInteger('unit_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tas_greenhouse_owner_units');
    }
};
