<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalesAreaManagersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sales_area_managers', function(Blueprint $table)
		{
			$table->integer('sales_area_manager_id', true);
			$table->string('ECN', 150);
			$table->string('dash', 155);
			$table->string('password');
			$table->text('api_token');
			$table->integer('zonal_manager_id');
			$table->integer('regional_manager_id');
			$table->integer('sales_regional_manager_id');
			$table->string('sales_area_manager_hq_code', 150);
			$table->string('sales_area_manager_name');
			$table->string('sales_area_manager_mobile');
			$table->string('sales_area_manager_pincode');
			$table->integer('status');
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
		Schema::drop('sales_area_managers');
	}

}
