<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThreadTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create( 'thread_tags', function( $table ){
            $table->increments( 'id' );
            $table->integer( 'target_id' );
            $table->integer( 'target_type' );
            $table->integer( 'tag_id' );
            $table->integer( 'status' );
            $table->integer( 'create_by' );
            $table->integer( 'create_time' );
            $table->integer( 'update_by' );
            $table->integer( 'update_time' );
            $table->integer( 'delete_by' )->default( 0 );
            $table->integer( 'delete_time' )->default( 0 );
            $table->text( 'reason' )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('thread_tags');
    }
}
