<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToPuppet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'puppets', function( $table ){
            $table->increments('id')->first();
            $table->integer('status');
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
        Schema::table( 'puppets', function( $table ){
            $table->dropColumn('id');
            $table->dropColumn('status');
            $table->dropColumn('create_time');
            $table->dropColumn('update_time');
        } );
    }
}
