<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class NewColumnAssignments extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::connection('db_parttime')->table('assignments', function (Blueprint $table) {
			$table->integer('upload_time')->unsigned()->default(0)->comment('上传时间，未上传时为0');
			$table->tinyInteger('grade')->unsigned()->default(0); //若已打分，则status应当为3
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::connection('db_parttime')->table('assignments', function (Blueprint $table) {
			$table->dropColumn('upload_time');
			$table->dropColumn('grade');
		});
	}
}
