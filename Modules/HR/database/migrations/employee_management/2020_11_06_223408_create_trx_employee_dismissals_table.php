<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxEmployeeDismissalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_employee_dismissals', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('employee_id');
			$table->integer('dismissal_reason_id');
            $table->bigInteger('director_acc_id')->nullable();
			$table->smallInteger('director_acc_status_id')->nullable();
			$table->timestamp('director_acc_time')->nullable();
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
        Schema::dropIfExists('trx_employee_dismissals');
    }
}
