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
			$table->integer('status')->default(1)->change();
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
