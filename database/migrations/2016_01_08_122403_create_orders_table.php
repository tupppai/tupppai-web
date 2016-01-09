<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    protected $connection = 'db_trade';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //dd(Schema::database('psgod_trade'));

        //后续可能作为总表进行拆分，1000w数量级
        //一库十表:uid维度
        //年库天表:time维度
        //Schema::connection('foo')->create('users', function($table)
        Schema::connection('db_trade')->create( 'orders', function( $table ){
            $table->increments( 'id' );
            $table->integer( 'uid' )->default(0);
            //订单号
            $table->string( 'order_no', 32 )->default('');
            //订单类型
            $table->integer( 'order_type' )->default(0);
            //交易类型
            $table->integer( 'trade_type' )->default(0);
            //销售类型
            $table->integer( 'sale_type' )->default(0);
            //支付类型
            $table->integer( 'payment_type' )->default(0);
            //支付总额
            $table->bigInteger( 'total_amount' )->default(0);
            //优惠券
            $table->integer( 'discount_id' )->default(0);
            $table->bigInteger( 'discount_amount' )->default(0);
            //手续费
            $table->bigInteger( 'handling_fee' )->default(0);
            //订单详细信息
            $table->string( 'order_info', 512 )->default('');
            //订单后台操作
            $table->string( 'operator', 255 )->default('');
            $table->string( 'op_remark', 255 )->default('');

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
        Schema::drop( 'orders' );
    }
}
