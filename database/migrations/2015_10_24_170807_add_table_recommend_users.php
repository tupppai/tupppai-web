<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableRecommendUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'recommendations', function( $table ){
            $table->increments( 'id' );
            $table->integer( 'introducer_uid' );
            $table->integer( 'uid' );
            $table->string( 'reason', '255' );
            $table->integer( 'status' );
            $table->string( 'result', '255' )->nullable();
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
        Schema::drop( 'recommendations' );
    }
}
