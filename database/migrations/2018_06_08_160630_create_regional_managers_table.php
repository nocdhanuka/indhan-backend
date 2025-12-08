<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRegionalManagersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('regional_managers', function(Blueprint $table)
		{
			$table->integer('regional_manager_id', true);
			$table->string('ECN', 150);
			$table->string('dash', 155);
			$table->string('password');
			$table->text('api_token');
			$table->integer('zonal_manager_id');
			$table->string('regional_manager_hq_code', 150);
			$table->string('regional_manager_name', 150);
			$table->string('regional_manager_mobile', 100);
			$table->integer('regional_manager_pincode');
			$table->integer('status')->comment('0=disabled, 1=enable');
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
		Schema::drop('regional_managers');
	}

}
