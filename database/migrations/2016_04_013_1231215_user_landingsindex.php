<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserLandingsindex extends Migration

{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_landings', function ($table) {
			//todo in好像会中断,需要优化下
			$table->index('uid');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_landings', function ($table) {
			$table->dropIndex('uid');
		});
	}
}
