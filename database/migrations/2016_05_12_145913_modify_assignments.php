<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ModifyAssignments extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::connection('db_parttime')->table('assignments', function (Blueprint $table) {
			$table->tinyInteger('reason_type')->unsigned()->default(0)->comment('主动拒绝时的拒绝选项，图片不合理/图片太难');
			$table->tinyInteger('grade_type')->unsigned()->default(0)->comment('打分理由，若为0则代表打分通过，不为0则代表打分为0，此处为不通过的理由选项');
			$table->string('grade_reason')->comment('打分理由，选填');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::connection('db_parttime')->table('assignments', function (Blueprint $table) {
			$table->dropColumn('reason_type');
			$table->dropColumn('grade_type');
			$table->dropColumn('grade_reason');
		});
	}
}
