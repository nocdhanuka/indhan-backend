<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateZonalManagerProductCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('zonal_manager_product_categories', function(Blueprint $table)
		{
			$table->integer('zonal_manager_product_category_id', true);
			$table->integer('zonal_manager_id');
			$table->integer('product_crop_category_id');
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
		Schema::drop('zonal_manager_product_categories');
	}

}
