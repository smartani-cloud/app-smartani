<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrefPscGradeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tref_psc_grade_details', function (Blueprint $table) {
            $table->increments('id');
			$table->smallInteger('set_id');
			$table->string('name',5);
			$table->double('start',4,3);
			$table->double('end',4,3);
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
        Schema::dropIfExists('tref_psc_grade_details');
    }
}
