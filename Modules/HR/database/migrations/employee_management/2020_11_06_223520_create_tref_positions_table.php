<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrefPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tref_positions', function (Blueprint $table) {
            $table->increments('id');
			$table->string('code');
            $table->string('name');
			$table->string('desc')->nullable();
			$table->tinyInteger('placement_id')->nullable();
			$table->tinyInteger('category_id');
			$table->integer('acc_position_id')->nullable();
			$table->smallInteger('status_id')->nullable();
			$table->bigInteger('role_id');
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
        Schema::dropIfExists('tref_positions');
    }
}
