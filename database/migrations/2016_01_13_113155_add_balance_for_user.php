<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBalanceForUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'users', function( $table ){
            $table->bigInteger('balance')->before('user_score');
            $table->bigInteger('freezing')->before('user_score');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'users', function( $table ){
            $table->dropColumn( 'balance' );
            $table->dropColumn( 'freezing' );
        });
    }
}
