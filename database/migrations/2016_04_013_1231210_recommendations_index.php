<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecommendationsIndex extends Migration

{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('recommendations', function ($table) {
			//todo in好像会中断,需要优化下
			$table->index(['role_id', 'status']);
			$table->index(['uid', 'status', 'role_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('recommendations', function ($table) {
			$table->dropIndex(['role_id', 'status']);
			$table->dropIndex(['uid', 'status', 'role_id']);
		});
	}
}
