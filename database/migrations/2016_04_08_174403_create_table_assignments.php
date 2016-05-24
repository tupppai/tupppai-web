<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAssignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_parttime')->create('assignments', function( $table ){
            $table->increments('id');
            $table->integer('task_id');
            $table->integer('assigned_to');
            $table->integer('assigned_by');
            $table->integer('status');
            $table->string('refuse_reason', 255);
            $table->integer('create_time');
            $table->integer('update_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('db_parttime')->drop('assignments');
    }
}
