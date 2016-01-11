<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastReplyTimeToAsks extends Migration
{
    public function up()
    {
        Schema::table('asks', function( $table ){
            $table->integer('last_reply_time')->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'asks', function( $table ){
            $table->dropColumn( 'last_reply_time' );
        });
    }
}
