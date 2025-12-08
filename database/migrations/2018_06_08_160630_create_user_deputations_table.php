<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserDeputationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_deputations', function(Blueprint $table)
		{
			$table->integer('user_deputation_id', true);
			$table->integer('user_id');
			$table->integer('zonal_manager_id');
			$table->integer('regional_manager_id');
			$table->integer('sales_regional_manager_id');
			$table->integer('area_manager_id');
			$table->integer('sales_area_manager_id');
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
		Schema::drop('user_deputations');
	}

}
