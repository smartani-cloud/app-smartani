<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tm_employees', function (Blueprint $table) {
            $table->id();
			$table->string('name');
			$table->string('nickname');
			$table->string('photo')->nullable();
			$table->string('nip');
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
			$table->integer('position_id')->nullable();
			$table->smallInteger('unit_id')->nullable();
			$table->smallInteger('employee_status_id')->nullable();
			$table->date('join_date');
			$table->date('disjoin_date')->nullable();
			$table->smallInteger('join_badge_status_id')->nullable();
			$table->smallInteger('disjoin_badge_status_id')->nullable();
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
        Schema::dropIfExists('tm_employees');
    }
}
