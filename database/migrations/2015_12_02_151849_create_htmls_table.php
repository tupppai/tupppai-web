<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHtmlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'htmls', function( $table ){
            $table->increments( 'id' );
            $table->integer( 'create_by' );
            $table->integer( 'update_by' );
            $table->string( 'title', 255 );
            $table->string( 'path', 255 );
            $table->string( 'url', 257 );
            $table->integer( 'status' );
            $table->integer( 'create_time' );
            $table->integer( 'update_time' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('htmls');
    }
}
