<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxTrainingAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_training_attendances', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('employee_training_id');
			$table->bigInteger('employee_id');
			$table->bigInteger('hr_acc_id')->nullable();
			$table->smallInteger('attendance_status_id')->nullable();
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
        Schema::dropIfExists('trx_training_attendances');
    }
}
