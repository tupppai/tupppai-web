<?php namespace App\Http\Controllers\Api;

use App\Services\User as sUser;
use App\Trades\Transaction as tTransaction;

class MoneyController extends ControllerBase{

    public function __construct() {
        parent::__construct();

    }

    /**
     * 企业转账
     */
    public function transferAction() {
        \Pingpp\Pingpp::setApiKey(env('PINGPP_KEY'));

        $open_id = $this->post('open_id', 'string', 'opbgVuM--bmipPZsYwfnCrsW1pRE');
        //todo: 微信支付amount＊100
        $amount  = $this->post('amount', 'float', '100');

        if (!$open_id) {
            return error('OPEN_ID_NOT_EXIST', '请先绑定微信帐号');
        }
        if (!$amount) {
            return error('AMOUNT_NOT_EXIST');
        }

        $user = sUser::getUserByUid($this->_uid);
        if ($amount > $user->balance) {
            return error('WRONG_ARGUMENTS', '余额不足，提现失败');
        }
        if ($amount > 200) {
            return error('WRONG_ARGUMENTS', '提现金额不能大于200，提现失败');
        }

        $subject = '图派';
        $body    = '红包提现';
        $currency= 'cny';

        $trade = tTransaction::createTrade($this->_uid, '', '', tTransaction::PAYMENT_TYPE_WECHAT_TRANSFER, $amount, $subject, $body, $currency);

        \Pingpp\Transfer::create(
            array(
                'order_no'    => $trade->id,
                'app'         => array('id' => env('PINGPP_APP')),
                'channel'     => 'wx_pub',
                'amount'      => $amount, //金额在 100-20000 之间
                'currency'    => $currency,
                'type'        => 'b2c',
                'recipient'   => $open_id,
                'description' => '图派红包提现,绽放你的灵感'
            )
        );
    }

    /**
     * 充值
     */
    public function chargeAction() {
        $type = $this->post('type', 'int');
        $price= $this->post('price', 'float');
       
        $data = json_decode('{
              "id": "ch_Hm5uTSifDOuTy9iLeLPSurrD",
              "object": "charge",
              "created": 1425095528,
              "livemode": true,
              "paid": false,
              "refunded": false,
              "app": "app_1Gqj58ynP0mHeX1q",
              "channel": "alipay",
              "order_no": "123456789",
              "client_ip": "127.0.0.1",
              "amount": 100,
              "amount_settle": 0,
              "currency": "cny",
              "subject": "Your Subject",
              "body": "Your Body",
              "extra":{},
              "time_paid": null,
              "time_expire": 1425181928,
              "time_settle": null,
              "transaction_no": null,
              "refunds": {
                "object": "list",
                "url": "/v1/charges/ch_Hm5uTSifDOuTy9iLeLPSurrD/refunds",
                "has_more": false,
                "data": []
              },
              "amount_refunded": 0,
              "failure_code": null,
              "failure_msg": null,
              "credential": {
                "object": "credential",
                "alipay":{
                  "orderInfo": "_input_charset=\"utf-8\"&body=\"tsttest\"&it_b_pay=\"1440m\"¬ify_url=\"https%3A%2F%2Fapi.pingxx.com%2Fnotify%2Fcharges%2Fch_jH8uD0aLyzHG9Oiz5OKOeHu9\"&out_trade_no=\"1234dsf7uyttbj\"&partner=\"2008451959385940\"&payment_type=\"1\"&seller_id=\"2008451959385940\"&service=\"mobile.securitypay.pay\"&subject=\"test\"&total_fee=\"1.23\"&sign=\"dkxTeVhMMHV2dlRPNWl6WHI5cm56THVI\"&sign_type=\"RSA\""
                }
              },
              "description": null
            }'); 
        return $this->output($data);
    }

    /**
     * 红包提现
     */
    public function refundAction() {
        \Pingpp\Pingpp::setApiKey(env('PINGPP_KEY'));

        $open_id = $this->post('open_id', 'string', 'opbgVuM--bmipPZsYwfnCrsW1pRE');
        //todo: 微信支付amount＊100
        $amount  = $this->post('amount', 'float', '100');

        if (!$open_id) {
            return error('OPEN_ID_NOT_EXIST', '请先绑定微信帐号');
        }
        if (!$amount) {
            return error('AMOUNT_NOT_EXIST');
        }

        $user = sUser::getUserByUid($this->_uid);
        if ($amount > $user->balance) {
            return error('WRONG_ARGUMENTS', '余额不足，提现失败');
        }
        if ($amount > 200) {
            return error('WRONG_ARGUMENTS', '提现金额不能大于200，提现失败');
        }

        $subject = '图派';
        $body    = '红包提现';
        $currency= 'cny';

        $trade = tTransaction::createTrade($this->_uid, '', '', tTransaction::PAYMENT_TYPE_WECHAT_RED, $amount, $subject, $body, $currency);

        $red = \Pingpp\RedEnvelope::create(
            array(
                'order_no'    => $trade->id,
                //'transaction_no'    =>$trade->trade_no,
                'app'         => array('id' => env('PINGPP_APP')),
                'channel'     => 'wx_pub', //红包基于微信公众帐号，所以渠道是 wx_pub
                'amount'      => $amount, //金额在 100-20000 之间
                'currency'    => $currency,
                'subject'     => $subject,
                'body'        => $body,
                'extra'       => array(
                    'nick_name' => '图派',
                    'send_name' => '皮埃斯网络科技'
                ),//extra 需填入的参数请参阅 API 文档
                'recipient'   => $open_id,//指定用户的 open_id
                'description' => '图派红包提现,绽放你的灵感'
            )
        );

        return $this->output();
    }

}
