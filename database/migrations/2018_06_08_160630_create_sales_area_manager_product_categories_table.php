<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalesAreaManagerProductCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sales_area_manager_product_categories', function(Blueprint $table)
		{
			$table->integer('sales_area_manager_product_category_id', true);
			$table->integer('sales_area_manager_id');
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
		Schema::drop('sales_area_manager_product_categories');
	}

}
