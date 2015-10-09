<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdateTimeForUserScores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_scores', function( $table ){
            $table->integer('create_time');
            $table->integer('update_time');
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'user_scores', function( $table ){
            $table->dropColumn('update_time');
            $table->dropColumn('create_time');
        } );
    }
}
