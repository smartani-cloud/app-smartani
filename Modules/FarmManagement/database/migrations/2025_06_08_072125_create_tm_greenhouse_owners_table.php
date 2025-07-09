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
        Schema::create('tm_greenhouse_owners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
			$table->string('nickname');
			$table->string('photo')->nullable();
			$table->string('nik',20);
			$table->string('npwp',20)->nullable();
			$table->tinyInteger('gender_id');
			$table->string('birth_place');
			$table->date('birth_date');
			$table->string('address');
			$table->tinyInteger('rt');
			$table->tinyInteger('rw');
			$table->integer('region_id');
			$table->string('phone_number',15);
			$table->string('email');
			$table->smallInteger('unit_id')->nullable();
			$table->smallInteger('active_status_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tm_greenhouse_owners');
    }
};
