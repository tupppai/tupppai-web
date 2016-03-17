<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUnionidToUserLandings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table( 'user_landings', function( $table ){
            $table->string('unionid')->default('')->after('openid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table( 'user_landings', function( $table ){
            $table->dropColumn( 'unionid' );
        });
    }
}
