<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTehsilsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tehsils', function(Blueprint $table)
		{
			$table->integer('tehsil_id', true);
			$table->integer('district_id')->index('district_id');
			$table->string('tehsil', 150);
			$table->enum('status', array('0','1'))->comment('0-disabled, 1-enabled');
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
		Schema::drop('tehsils');
	}

}
