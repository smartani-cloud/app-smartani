<<<<<<< HEAD
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxEmployeePlacementDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_employee_placement_details', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('employee_placement_id');
			$table->bigInteger('employee_id');
			$table->Integer('position_id');
			$table->date('period_start')->nullable();
			$table->date('period_end')->nullable();
			$table->date('placement_date')->nullable();
			$table->Integer('acc_position_id')->nullable();
			$table->bigInteger('acc_employee_id')->nullable();
			$table->smallInteger('acc_status_id')->nullable();
			$table->timestamp('acc_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trx_employee_placement_details');
    }
}
=======
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxEmployeePlacementDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_employee_placement_details', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('employee_placement_id');
			$table->bigInteger('employee_id');
			$table->Integer('position_id');
			$table->date('period_start')->nullable();
			$table->date('period_end')->nullable();
			$table->date('placement_date')->nullable();
			$table->Integer('acc_position_id')->nullable();
			$table->bigInteger('acc_employee_id')->nullable();
			$table->smallInteger('acc_status_id')->nullable();
			$table->timestamp('acc_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trx_employee_placement_details');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
