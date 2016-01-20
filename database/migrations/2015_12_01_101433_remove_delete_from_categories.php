<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveDeleteFromCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'categories', function( $table ){
            $table->dropColumn( 'delete_by' );
            $table->dropColumn( 'delete_time' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'categories', function( $table ){
            $table->integer( 'delete_by' );
            $table->integer( 'delete_time' );
        });
    }
}
