<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class StatusDefaultAssignments extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::connection('db_parttime')->table('assignments', function (Blueprint $table) {
			$table->integer('status')->unsigned()->default(1)->comment('状态，0为已收回任务，1为已派发任务，2为任务已被下载，3为作品已提交，4为作品已评分')->change();

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::connection('db_parttime')->table('assignments', function (Blueprint $table) {
			$table->integer('status')->default(null)->change();
		});
	}
}
