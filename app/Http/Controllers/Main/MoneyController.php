<?php 
namespace App\Http\Controllers\Main;

use App\Models\ActionLog;

use App\Services\User;

class MoneyController extends ControllerBase {

    public function pay() {
        $type = $this->post('type', 'int');
        $price= $this->post('price', 'float');
        $data = '{
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
        }';
        return $this->output($data);
    }

    /*
	public function pay(){
        // api_key、app_id 请从 [Dashboard](https://dashboard.pingxx.com) 获取
        $api_key = 'sk_test_ibbTe5jLGCi5rzfH4OqPW9KC';
        $app_id = 'app_1Gqj58ynP0mHeX1q';

        // 此处为 Content-Type 是 application/json 时获取 POST 参数的示例
        $input_data = json_decode(file_get_contents('php://input'), true);
        if (empty($input_data['channel']) || empty($input_data['amount'])) {
            echo 'channel or amount is empty';
            exit();
        }
        $channel = strtolower($input_data['channel']);
        $amount = $input_data['amount'];
        $orderNo = substr(md5(time()), 0, 12);

         //* $extra 在使用某些渠道的时候，需要填入相应的参数，其它渠道则是 array()。
         //* 以下 channel 仅为部分示例，未列出的 channel 请查看文档 https://pingxx.com/document/api#api-c-new
        $extra = array();
        switch ($channel) {
            case 'alipay_wap':
                $extra = array(
                    'success_url' => 'http://example.com/success',
                    'cancel_url' => 'http://example.com/cancel'
                );
                break;
            case 'bfb_wap':
                $extra = array(
                    'result_url' => 'http://example.com/result',
                    'bfb_login' => true
                );
                break;
            case 'upacp_wap':
                $extra = array(
                    'result_url' => 'http://example.com/result'
                );
                break;
            case 'wx_pub':
                $extra = array(
                    'open_id' => 'openidxxxxxxxxxxxx'
                );
                break;
            case 'wx_pub_qr':
                $extra = array(
                    'product_id' => 'Productid'
                );
                break;
            case 'yeepay_wap':
                $extra = array(
                    'product_category' => '1',
                    'identity_id'=> 'your identity_id',
                    'identity_type' => 1,
                    'terminal_type' => 1,
                    'terminal_id'=>'your terminal_id',
                    'user_ua'=>'your user_ua',
                    'result_url'=>'http://example.com/result'
                );
                break;
            case 'jdpay_wap':
                $extra = array(
                    'success_url' => 'http://example.com/success',
                    'fail_url'=> 'http://example.com/fail',
                    'token' => 'dsafadsfasdfadsjuyhfnhujkijunhaf'
                );
                break;
        }

        // 设置 API Key
        \Pingpp\Pingpp::setApiKey($api_key);
        try {
            $ch = \Pingpp\Charge::create(
                array( 
                    'subject'   => 'Your Subject', //商品名
                    'body'      => 'Your Body',  //商品描述
                    'amount'    => $amount, //金额
                    'order_no'  => $orderNo, //订单号
                    'currency'  => 'cny', //货币种类
                    'extra'     => 'alipay_pc_direct', // 支付参数
                    'channel'   => $channel, //支付方式
                    'client_ip' => $_SERVER['REMOTE_ADDR'], //发起支付的IP
                    'app'       => array('id' => $app_id)
                )
            );
            echo $ch;
        } catch (\Pingpp\Error\Base $e) {
            header('Status: ' . $e->getHttpStatus());
            // 捕获报错信息
            echo $e->getHttpBody();
        }


    }
     */
}
