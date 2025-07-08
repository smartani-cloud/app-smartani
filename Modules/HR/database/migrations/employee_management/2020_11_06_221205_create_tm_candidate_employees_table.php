<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmCandidateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tm_candidate_employees', function (Blueprint $table) {
            $table->id();
			$table->string('name');
			$table->string('nickname');
			$table->string('photo')->nullable();
			$table->string('nik',20);
			$table->string('npwp',20)->nullable();
			$table->tinyInteger('gender_id');
			$table->string('birth_place');
			$table->date('birth_date');
			$table->tinyInteger('marital_status_id');
			$table->string('address');
			$table->tinyInteger('rt');
			$table->tinyInteger('rw');
			$table->integer('region_id');
			$table->string('phone_number',15);
			$table->string('email');
			$table->smallInteger('education_level_id')->nullable();
			$table->integer('academic_background_id')->nullable();
			$table->bigInteger('university_id')->nullable();
			$table->string('competency_test')->nullable();
			$table->string('psychological_test')->nullable();
			$table->smallInteger('acceptance_status_id')->nullable();
            $table->integer('position_id')->nullable();
			$table->smallInteger('unit_id')->nullable();
			$table->smallInteger('employee_status_id')->nullable();			
			$table->date('period_start')->nullable();
			$table->date('period_end')->nullable();
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
        Schema::dropIfExists('tm_candidate_employees');
    }
}
