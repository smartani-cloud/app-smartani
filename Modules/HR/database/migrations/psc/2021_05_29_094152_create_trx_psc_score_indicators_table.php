<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxPscScoreIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_psc_score_indicators', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('psc_score_id');
			$table->string('code',5)->nullable();
			$table->bigInteger('indicator_id');
			$table->double('score',4,3)->default(0);
			$table->smallInteger('percentage')->nullable();
			$table->double('total_score',4,3)->default(0);
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
        Schema::dropIfExists('trx_psc_score_indicators');
    }
}
