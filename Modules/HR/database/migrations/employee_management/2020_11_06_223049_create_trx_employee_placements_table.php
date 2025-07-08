<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxEmployeePlacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_employee_placements', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('academic_year_id')->nullable();
			$table->year('year')->nullable();
			$table->smallInteger('unit_id');
			$table->tinyInteger('placement_id');
			$table->smallInteger('status_id')->nullable();
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
        Schema::dropIfExists('trx_employee_placements');
    }
}
