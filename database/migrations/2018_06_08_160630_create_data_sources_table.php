<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDataSourcesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('data_sources', function(Blueprint $table)
		{
			$table->integer('data_source_id', true);
			$table->string('data_source', 150);
			$table->enum('status', array('0','1'))->comment('0-inactive, 1-active');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('data_sources');
	}

}
