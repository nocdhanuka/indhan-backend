<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppMenuPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('app_menu_permissions', function(Blueprint $table)
		{
			$table->integer('app_menu_permission_id', true);
			$table->string('department', 100)->comment('zonal. regional, area, bdo');
			$table->integer('app_menu_id');
			$table->integer('user_id')->nullable();
			$table->string('user_type', 100)->nullable()->comment('zonal. regional, area, bdo');
			$table->string('status', 10);
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
		Schema::drop('app_menu_permissions');
	}

}
