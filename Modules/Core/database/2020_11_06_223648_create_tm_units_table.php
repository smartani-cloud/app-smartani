<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tm_units', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name');
            $table->string('full_name')->nullable();
			$table->string('show_name')->nullable();
			$table->string('address')->nullable();			
            $table->string('postal_code',6)->nullable();
			$table->integer('region_id')->nullable();
			$table->string('phone_unit')->nullable();
			$table->string('email')->nullable();
			$table->string('website')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tref_unit');
    }
}
