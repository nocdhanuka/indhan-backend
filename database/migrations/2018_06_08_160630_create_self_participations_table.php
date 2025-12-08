<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSelfParticipationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('self_participations', function(Blueprint $table)
		{
			$table->integer('self_participation_id', true);
			$table->integer('ticket_id');
			$table->integer('user_id');
			$table->string('user_type', 150);
			$table->text('description');
			$table->string('uploaded_file');
			$table->integer('status');
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->dateTime('uploaded_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('self_participations');
	}

}
