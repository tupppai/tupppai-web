<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToTransactions extends Migration
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
            $table->string('callback_id', 50)->default('')->after('trade_finish_time');
            $table->string('currency_type', 10)->change();
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
            $table->dropColumn('callback_id');
            $table->integer('currency_type')->change();
        });
    }
}
