<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
			$table->string('username')->unique()->after('name');
			$table->unsignedInteger('role_id')->nullable()->after('password');			
            $table->smallInteger('status_id')->after('remember_token')->default(5);
			$table->string('name')->nullable()->change();
			$table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
			$table->dropColumn('role_id');
			$table->dropColumn('status_id');
			$table->string('name')->change();
			$table->string('email')->change();
        });
    }
}
