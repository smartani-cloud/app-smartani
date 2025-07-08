<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxPscScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_psc_scores', function (Blueprint $table) {
            $table->id();
			$table->smallInteger('unit_id');
			$table->integer('position_id');			
			$table->string('position_name')->nullable();
			$table->bigInteger('academic_year_id')->nullable();
			$table->year('year')->nullable();
			$table->bigInteger('employee_id');
			$table->string('employee_name')->nullable();
			$table->double('total_score',4,3)->nullable();
			$table->bigInteger('grade_id')->nullable();
			$table->string('grade_name',5)->nullable();
			$table->integer('psc_grade_record_id')->nullable();
			$table->integer('validator_id')->nullable();
			$table->bigInteger('acc_employee_id')->nullable();
			$table->smallInteger('acc_status_id')->nullable();
			$table->timestamp('acc_time')->nullable();
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
        Schema::dropIfExists('trx_psc_scores');
    }
}
