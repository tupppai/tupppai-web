<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'categories', function( $table ){
            $table->increments( 'id' );
            $table->string( 'name', 50 );
            $table->string( 'display_name', 10 );
            $table->string( 'description', 255 )->nullable();
            $table->integer( 'pid' )->default( 0 );
            $table->integer( 'status' );
            $table->integer( 'order' )->default( 0 );
            $table->integer( 'create_by' );
            $table->integer( 'create_time' );
            $table->integer( 'update_by' );
            $table->integer( 'update_time' );
            $table->integer( 'delete_by' )->default( 0 );
            $table->integer( 'delete_time' )->default( 0 );
        } );


        Schema::create( 'thread_categories', function( $table ){
            $table->increments( 'id' );
            //$table->morphs( 'target' ); int:_id & str:_type
            $table->integer( 'target_id' );
            $table->integer( 'target_type' );
            $table->string( 'category_ids', 255 );
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
        Schema::drop('categories');
        Schema::drop('thread_categories');
    }
}
