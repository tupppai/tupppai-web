<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBalanceForUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_trade')->table( 'accounts', function( $table ){
            $table->dropColumn( 'income_amount' );
            $table->dropColumn( 'outcome_amount' );
            $table->dropColumn( 'freeze_amount' );

            $table->integer('type')->after('balance')->default(0);
            $table->bigInteger('amount')->after('type')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('db_trade')->table( 'users', function( $table ){
            $table->bigInteger( 'income_amount' )->before('user_score');
            $table->bigInteger( 'outcome_amount' )->before('user_score');
            $table->bigInteger( 'freeze_amount' )->before('user_score');

            $table->dropColumn( 'type' );
            $table->dropColumn( 'amount' );
        });
    }
}
