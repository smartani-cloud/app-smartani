<<<<<<< HEAD
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogEmployeePlacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_employee_placements', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('employee_placement_id');
			$table->string('employee_name');
			$table->string('employee_nip');
			$table->string('employee_birth_place');
			$table->date('employee_birth_date');
			$table->string('position_name');
			$table->date('period_start')->nullable();
			$table->date('period_end')->nullable();
			$table->date('placement_date')->nullable();
			$table->string('acc_position')->nullable();
			$table->string('acc_name')->nullable();
			$table->timestamp('acc_time')->nullable();
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
        Schema::dropIfExists('log_employee_placements');
    }
}
=======
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogEmployeePlacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_employee_placements', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('employee_placement_id');
			$table->string('employee_name');
			$table->string('employee_nip');
			$table->string('employee_birth_place');
			$table->date('employee_birth_date');
			$table->string('position_name');
			$table->date('period_start')->nullable();
			$table->date('period_end')->nullable();
			$table->date('placement_date')->nullable();
			$table->string('acc_position')->nullable();
			$table->string('acc_name')->nullable();
			$table->timestamp('acc_time')->nullable();
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
        Schema::dropIfExists('log_employee_placements');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
