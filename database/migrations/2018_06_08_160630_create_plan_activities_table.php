<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlanActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('plan_activities', function(Blueprint $table)
		{
			$table->integer('plan_activity_id', true);
			$table->integer('user_id');
			$table->string('month', 5);
			$table->integer('product_id');
			$table->integer('crop_id');
			$table->integer('days');
			$table->integer('dd');
			$table->string('record_id');
			$table->integer('status');
			$table->integer('created_by_user_id');
			$table->string('user_type', 100);
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
		Schema::drop('plan_activities');
	}

}
