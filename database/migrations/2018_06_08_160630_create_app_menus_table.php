<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('app_menus', function(Blueprint $table)
		{
			$table->integer('menu_id', true);
			$table->string('menu');
			$table->string('slag');
			$table->string('image');
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
		Schema::drop('app_menus');
	}

}
