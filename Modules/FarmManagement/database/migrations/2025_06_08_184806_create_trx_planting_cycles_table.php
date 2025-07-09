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
        Schema::create('trx_planting_cycles', function (Blueprint $table) {
            $table->id();            
			$table->unsignedSmallInteger('unit_id');
			$table->string('id_planting_cycle', 21)->unique();
            $table->unsignedBigInteger('plant_id');
			$table->date('seeding_date');
			$table->date('transplanting_date')->nullable(); // Tanggal pemindahan bibit ke lahan tanam, jika tidak ada, diasumsikan langsung tanam
			$table->integer('total_seed_holes');
			$table->integer('irrigation_duration_seconds')->default(0); // Durasi penyiraman dalam detik
			$table->decimal('capital_cost', 12, 2)->default(0); // Modal yang digunakan dalam siklus tanam
			$table->decimal('min_yield_kg', 8, 2)->default(0); // Perkiraan hasil panen minimum (kg)
			$table->decimal('max_yield_kg', 8, 2)->default(0); // Perkiraan hasil panen maksimum (kg)
			$table->decimal('total_yield_kg', 8, 2)->default(0); // Total hasil panen yang dicapai (kg)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_planting_cycles');
    }
};
