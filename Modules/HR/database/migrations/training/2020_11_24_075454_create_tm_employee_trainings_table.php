<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmEmployeeTrainingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tm_employee_trainings', function (Blueprint $table) {
            $table->id();
			$table->string('number')->nullable();
			$table->string('name');
			$table->string('desc')->nullable();
			$table->date('date')->nullable();
			$table->string('place')->nullable();
			$table->bigInteger('speaker_id')->nullable();
			$table->string('speaker_name')->nullable();
			$table->bigInteger('academic_year_id')->nullable();
			$table->bigInteger('semester_id')->nullable();
			$table->smallInteger('mandatory_status_id')->nullable();
			$table->smallInteger('organizer_id');
            $table->bigInteger('hr_acc_id')->nullable();
			$table->smallInteger('hr_acc_status_id')->nullable();
			$table->timestamp('hr_acc_time')->nullable();			
            $table->smallInteger('active_status_id')->nullable();
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
        Schema::dropIfExists('tm_employee_trainings');
    }
}
