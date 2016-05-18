<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ThreadTagsIndex extends Migration

{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('thread_tags', function ($table) {
			//todo in好像会中断,需要优化下
			$table->index(['target_id', 'target_type']);
			$table->index('tag_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('thread_tags', function ($table) {
			$table->dropIndex(['target_id', 'target_type']);
			$table->dropIndex('tag_id');
		});
	}
}
