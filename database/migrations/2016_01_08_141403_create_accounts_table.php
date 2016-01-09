<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    public $connection = 'db_trade';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_trade')->create( 'accounts', function( $table ){
            $table->increments( 'id' );
            $table->integer( 'uid' )->default(0);
            $table->bigInteger( 'balance' )->default(0);
            $table->bigInteger( 'income_amount' )->default(0);
            $table->bigInteger( 'outcome_amount' )->default(0);
            $table->bigInteger( 'freeze_amount' )->default(0);
            $table->string( 'memo', 255 )->default('');
            $table->integer( 'status' )->default(0);

            $table->softDeletes();
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop( 'accounts' );
    }
}
