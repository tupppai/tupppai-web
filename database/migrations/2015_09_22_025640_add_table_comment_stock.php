<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCommentStock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments_stock', function( $table ){
            $table->increments( 'id' );
            $table->integer( 'owner_uid' );
            $table->string( 'content', 255 );
            $table->integer( 'sort' )->default( 0 );
            $table->integer( 'used_times' )->default( 0 );
            $table->integer( 'create_time' );
            $table->integer( 'update_time' );
            $table->integer( 'status' )->default( 1 );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop( 'comments_stock' );
    }
}
