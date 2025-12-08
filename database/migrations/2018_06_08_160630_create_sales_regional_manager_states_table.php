<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalesRegionalManagerStatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sales_regional_manager_states', function(Blueprint $table)
		{
			$table->integer('sales_regional_manager_state_id', true);
			$table->integer('sales_regional_manager_id');
			$table->integer('state_id');
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
		Schema::drop('sales_regional_manager_states');
	}

}
