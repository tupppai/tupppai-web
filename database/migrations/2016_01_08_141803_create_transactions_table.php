<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    public $connection = 'db_trade';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //后续可能作为总表进行拆分，1000w数量级
        //一库十表:uid维度
        //年库天表:time维度
        Schema::connection('db_trade')->create( 'transactions', function( $table ){
            $table->increments( 'id' );
            //交易号
            $table->string( 'trade_no', 255 )->default(''); 
            //商户订单号
            $table->string( 'out_trade_no', 255 )->default('');
            //订单id
            $table->integer( 'order_id' )->default(0);
            $table->string( 'partner_id', 64 )->default('');
            //支付类型
            $table->integer( 'payment_type' )->default(0);
            //订单价格
            $table->bigInteger( 'amount' )->default(0);
            $table->integer( 'trade_status' )->default(0);
            $table->datetime( 'trade_start_time' )->default('0000-00-00 0:00:00');
            $table->datetime( 'trade_finish_time' )->default('0000-00-00 0:00:00');
            $table->integer( 'callback_status' )->default(0);
            $table->datetime( 'callback_finish_time' )->default('0000-00-00 0:00:00');
            $table->integer( 'refund_status' )->default(0);
            $table->datetime( 'refund_start_time' )->default('0000-00-00 0:00:00');
            $table->datetime( 'refund_finish_time' )->default('0000-00-00 0:00:00');

            //交易开始结束时间
            $table->datetime( 'time_start' )->default('0000-00-00 0:00:00');
            $table->datetime( 'time_expire' )->default('0000-00-00 0:00:00');

            //各平台需要的url
            $table->string( 'return_url', 255 )->default('');
            $table->string( 'fail_url', 255 )->default('');
            $table->string( 'notify_url', 255 )->default('');

            //主题
            $table->string( 'subject', 255 )->default('');
            //具体内容
            $table->string( 'body', 255 )->default('');
            //货币类型
            $table->integer( 'currency_type' )->default(0);
            //客户端ip
            $table->string( 'client_ip', 255 )->default('');
            //用户请求额外数据
            $table->string( 'attach', 255 )->default('');
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
        Schema::drop( 'transactions' );
    }
}
