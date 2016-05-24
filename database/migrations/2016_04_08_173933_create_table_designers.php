<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDesigners extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_parttime')->create('designers', function( $table ){
            $table->integer('uid');
            $table->double('ability')->default( 0 );
            $table->integer('max_tasks')->default(1);
            $table->integer('status')->default( 1 );
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
        Schema::connection('db_parttime')->drop('designers');
    }
}
