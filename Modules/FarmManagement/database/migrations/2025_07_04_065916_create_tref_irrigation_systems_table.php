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
        Schema::create('tref_irrigation_systems', function (Blueprint $table) {
            $table->tinyIncrements('id');
			$table->string('code')->unique();
			$table->string('name');
			$table->text('desc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tref_irrigation_systems');
    }
};
