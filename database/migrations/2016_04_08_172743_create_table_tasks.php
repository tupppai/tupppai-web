<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_parttime')->create('tasks',function( $table ){
            $table->increments('id');
            $table->integer('target_type');
            $table->integer('target_id');
            $table->double('priority')->default( 0 );
            $table->integer('designer_needed')->default( 1 );
            $table->integer('status')->default( 1 );
            $table->boolean('has_parttime_replied')->default( false );
            $table->boolean('has_user_replied')->default( false );
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
        Schema::connection('db_parttime')->drop('tasks');
    }
}
