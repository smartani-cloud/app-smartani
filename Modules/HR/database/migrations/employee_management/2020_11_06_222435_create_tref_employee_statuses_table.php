<<<<<<< HEAD
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrefEmployeeStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tref_employee_statuses', function (Blueprint $table) {
            $table->smallIncrements('id');
			$table->string('code');
			$table->string('status');
			$table->string('show_name',15)->nullable();
			$table->text('desc')->nullable();
            $table->integer('category_id')->nullable();
			$table->smallInteger('status_id');
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
        Schema::dropIfExists('tref_employee_statuses');
    }
}
=======
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrefEmployeeStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tref_employee_statuses', function (Blueprint $table) {
            $table->smallIncrements('id');
			$table->string('code');
			$table->string('status');
			$table->string('show_name',15)->nullable();
			$table->text('desc')->nullable();
            $table->integer('category_id')->nullable();
			$table->smallInteger('status_id');
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
        Schema::dropIfExists('tref_employee_statuses');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
