<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnRefundUrlToTransactions extends Migration
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
            $table->string('refund_url', 255)->default('')->before('refund_status');
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
            $table->dropColumn( 'refund_url' );
        });
    }
}
