<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveColumnAssignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_parttime')->table('assignments', function (Blueprint $table) {
            $table->dropColumn('task_id');
            $table->dropColumn('assigned_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('db_parttime')->table('assignments', function (Blueprint $table) {
            $table->integer('task_id');
            $table->integer('assigned_by');
        });
    }
}
