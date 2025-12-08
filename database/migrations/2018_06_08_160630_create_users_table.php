<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('ECN', 150);
			$table->string('password');
			$table->string('dash', 150);
			$table->text('api_token');
			$table->integer('zonal_manager_id');
			$table->integer('regional_manager_id');
			$table->integer('sales_regional_manager_id');
			$table->integer('area_manager_id');
			$table->integer('sales_area_manager_id');
			$table->integer('state_id');
			$table->integer('district_id');
			$table->integer('language_id');
			$table->string('name');
			$table->string('bdo_code', 150);
			$table->string('bdo_hq', 150);
			$table->string('bdo_mobile_no', 150);
			$table->string('department', 222);
			$table->string('designation', 222);
			$table->enum('app_permission_edit_ticket', array('0','1'))->default('1')->comment('0=disabled, 1=enable');
			$table->enum('app_permission_close_ticket', array('0','1'))->default('1')->comment('0=disabled, 1=enable');
			$table->string('status', 50)->comment('0=disabled, 1=enable');
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
		Schema::drop('users');
	}

}
