<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFarmersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('farmers', function(Blueprint $table)
		{
			$table->integer('farmer_id', true);
			$table->integer('ticket_id')->index('ticket_id');
			$table->integer('user_id');
			$table->string('user_type', 155)->comment('zonal. regional, area, bdo');
			$table->string('mobile');
			$table->dateTime('datetime');
			$table->string('registration_type', 50);
			$table->integer('ticket_status')->default(1)->comment('0=deleted, 1=active');
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
		Schema::drop('farmers');
	}

}
