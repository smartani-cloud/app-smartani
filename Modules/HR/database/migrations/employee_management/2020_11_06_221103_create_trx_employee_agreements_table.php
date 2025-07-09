<<<<<<< HEAD
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxEmployeeAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_employee_agreements', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('employee_id');
			$table->string('reference_number')->nullable();
			$table->string('party_1_name');
			$table->string('party_1_position');
			$table->text('party_1_address');
			$table->string('employee_name');
			$table->text('employee_address');
			$table->string('employee_status');
			$table->date('period_start')->nullable();
			$table->date('period_end')->nullable();
			$table->integer('status_id');
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
        Schema::dropIfExists('trx_employee_agreements');
    }
}
=======
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxEmployeeAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_employee_agreements', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('employee_id');
			$table->string('reference_number')->nullable();
			$table->string('party_1_name');
			$table->string('party_1_position');
			$table->text('party_1_address');
			$table->string('employee_name');
			$table->text('employee_address');
			$table->string('employee_status');
			$table->date('period_start')->nullable();
			$table->date('period_end')->nullable();
			$table->integer('status_id');
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
        Schema::dropIfExists('trx_employee_agreements');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
