<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InformsIndex extends Migration

{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('informs', function ($table) {
			//todo in好像会中断,需要优化下
			$table->index(['target_type','target_id']);
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
		Schema::table('informs', function ($table) {
			$table->dropIndex(['target_type','target_id']);
			$table->dropIndex(['uid']);
		});
	}
}
