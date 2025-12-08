<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductCropRelationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_crop_relations', function(Blueprint $table)
		{
			$table->integer('product_crop_relation_id', true);
			$table->integer('product_crop_category_id');
			$table->integer('product_id');
			$table->integer('crop_id');
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
		Schema::drop('product_crop_relations');
	}

}
