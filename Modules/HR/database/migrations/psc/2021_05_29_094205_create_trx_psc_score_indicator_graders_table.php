<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxPscScoreIndicatorGradersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_psc_score_indicator_graders', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('psi_id');
			$table->double('score',4,3)->default(0);
			$table->bigInteger('grader_id');
			$table->integer('position_id');
			$table->string('position_desc')->nullable();
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
        Schema::dropIfExists('trx_psc_score_indicator_graders');
    }
}
