<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxEmployeeEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_employee_evaluations', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('employee_id');
			$table->integer('temp_psc_grade_id')->nullable();
			$table->string('supervision_result')->nullable();
			$table->string('interview_result')->nullable();
			$table->smallInteger('recommend_status_id')->nullable();
			$table->smallInteger('recommended_employee_status_id')->nullable();
			$table->integer('dismissal_reason_id')->nullable();
            $table->bigInteger('hr_acc_id')->nullable();
			$table->smallInteger('hr_acc_status_id')->nullable();
			$table->timestamp('hr_acc_time')->nullable();
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
        Schema::dropIfExists('trx_employee_evaluations');
    }
}
