<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnExtraToTradeAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::connection('db_trade')->table( 'accounts', function( $table ){
            $table->string('extra', 255)->default('')->after('memo');
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
        Schema::connection('db_trade')->table( 'accounts', function( $table ){
            $table->dropColumn( 'extra' );
        });
    }
}
