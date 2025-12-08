<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('settings', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('title', 250);
			$table->integer('religion');
			$table->integer('place');
			$table->integer('education');
			$table->integer('occupation');
			$table->integer('age');
			$table->string('search_without_login', 500);
			$table->string('smtp_username', 250);
			$table->string('smtp_host', 250);
			$table->string('smtp_password', 250);
			$table->string('currency', 500);
			$table->string('fav_icon', 500);
			$table->string('image', 500);
			$table->string('payment_gateway_username', 500);
			$table->string('payment_gateway_password', 500);
			$table->string('payment_gateway_signature', 500);
			$table->string('payment_gateway_testmode', 500);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('settings');
	}

}
