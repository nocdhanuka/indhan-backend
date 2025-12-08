<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAreaManagersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('area_managers', function(Blueprint $table)
		{
			$table->integer('area_manager_id', true);
			$table->string('ECN', 150);
			$table->string('dash', 155);
			$table->string('password');
			$table->text('api_token')->nullable();
			$table->integer('zonal_manager_id');
			$table->integer('regional_manager_id');
			$table->integer('sales_regional_manager_id');
			$table->string('area_manager_hq_code', 150);
			$table->string('area_manager_name', 150);
			$table->string('area_manager_mobile', 100);
			$table->integer('area_manager_pincode');
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
		Schema::drop('area_managers');
	}

}
