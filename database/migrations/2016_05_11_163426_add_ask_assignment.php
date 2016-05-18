<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddAskAssignment extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::connection('db_parttime')->table('assignments', function (Blueprint $table) {
			$table->integer('ask_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::connection('db_parttime')->table('assignments', function (Blueprint $table) {
			$table->dropColumn('ask_id');
		});
	}
}
