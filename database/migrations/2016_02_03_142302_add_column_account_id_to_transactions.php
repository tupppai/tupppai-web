<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAccountIdToTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::connection('db_trade')->table( 'transactions', function( $table ){
            $table->integer('account_id')->default(0)->after('order_id');
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
        Schema::connection('db_trade')->table( 'transactions', function( $table ){
            $table->dropColumn( 'account_id' );
        });
    }
}
