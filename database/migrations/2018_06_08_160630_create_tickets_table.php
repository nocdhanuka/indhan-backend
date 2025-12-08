<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTicketsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tickets', function(Blueprint $table)
		{
			$table->integer('ticket_id', true);
			$table->string('ticket_no');
			$table->integer('user_id');
			$table->integer('plan_atp_id');
			$table->string('user_type', 50);
			$table->integer('data_source_id');
			$table->integer('state_id');
			$table->integer('district_id');
			$table->integer('tehsil_id');
			$table->string('village');
			$table->integer('product_1_id');
			$table->integer('crop_1_id');
			$table->integer('product_2_id');
			$table->integer('crop_2_id');
			$table->integer('product_3_id');
			$table->integer('crop_3_id');
			$table->integer('expected_farmers');
			$table->integer('expected_pda');
			$table->string('sp_name_1', 150);
			$table->string('sp_number_1', 150);
			$table->string('sp_name_2', 150);
			$table->string('sp_number_2', 150);
			$table->string('sp_name_3', 150);
			$table->string('sp_number_3', 150);
			$table->string('sp_name_4', 150);
			$table->string('sp_number_4', 150);
			$table->string('sp_name_5', 150);
			$table->string('sp_number_5', 150);
			$table->string('sp_name_6', 150);
			$table->string('sp_number_6', 150);
			$table->string('uploaded_file')->nullable();
			$table->enum('status', array('0','1'))->comment('0=disabled, 1=enable');
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
		Schema::drop('tickets');
	}

}
