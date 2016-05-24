<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RefuseTypeAssignments extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::connection('db_parttime')->table('assignments', function (Blueprint $table) {
			$table->tinyInteger('refuse_type')->default(0)->unsigned(); //代表拒绝类型，1为超时后自动回收，2为用户拒绝
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::connection('db_parttime')->table('assignments', function (Blueprint $table) {
			$table->dropColumn('refuse_type');
		});
	}
}
