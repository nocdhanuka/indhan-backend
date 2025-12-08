<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlanAtpTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('plan_atp', function(Blueprint $table)
		{
			$table->integer('plan_atp_id', true);
			$table->string('plan_date', 100);
			$table->integer('product_id');
			$table->integer('crop_id');
			$table->integer('data_source_id');
			$table->integer('state_id');
			$table->integer('district_id');
			$table->integer('tehsil_id');
			$table->string('village', 150);
			$table->integer('expected_farmer');
			$table->integer('expected_pda');
			$table->integer('created_by_user_id');
			$table->string('user_type', 50);
			$table->string('record_id', 155);
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
		Schema::drop('plan_atp');
	}

}
