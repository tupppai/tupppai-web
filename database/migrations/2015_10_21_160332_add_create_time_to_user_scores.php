<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreateTimeToUserScores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'user_scores', function( $table ){
            $table->integer('create_time')
                ->nullable()
                ->after('action_time')
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'user_scores', function( $table ){
            $table->dropColumn('create_time');
        } );
    }
}
