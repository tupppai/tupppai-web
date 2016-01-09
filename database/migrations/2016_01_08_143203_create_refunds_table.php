<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundsTable extends Migration
{
    public $connection = 'db_trade';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //后续可能作为总表进行拆分，
        //一库十表:uid维度
        //年库天表:time维度
        Schema::connection('db_trade')->create( 'refunds', function( $table ){
            $table->increments( 'id' );
            $table->integer( 'uid' )->default(0);
            $table->bigInteger( 'amount' )->default(0);
            //银行信息
            $table->string( 'bank_type', 32)->default('');
            $table->string( 'bank_branch', 64 )->default('');
            //信用卡信息
            $table->string( 'card_no', 64 )->default('');
            $table->string( 'name', 64 )->default('');
            $table->string( 'mobile', 64 )->default('');
            $table->datetime( 'account_date' )->default('0000-00-00 0:00:00');
            //订单后台操作
            $table->string( 'operator', 255 )->default('');
            $table->string( 'op_remark', 255 )->default('');
            //订单备注
            $table->string( 'remark', 255 )->default('');
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
        Schema::drop( 'refunds' );
    }
}
