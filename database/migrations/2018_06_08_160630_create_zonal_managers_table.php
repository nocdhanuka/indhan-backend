<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateZonalManagersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('zonal_managers', function(Blueprint $table)
		{
			$table->integer('zonal_manager_id', true);
			$table->string('ECN', 150);
			$table->string('dash', 155);
			$table->string('password');
			$table->text('api_token');
			$table->string('zonal_manager_name', 150);
			$table->string('zonal_manager_mobile', 50);
			$table->string('zonal_manager_pincode', 50);
			$table->integer('status')->default(1)->comment('0=disabled, 1=enable');
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
		Schema::drop('zonal_managers');
	}

}
