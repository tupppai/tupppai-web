<?php namespace App\Http\Controllers\Api;

class MoneyController extends ControllerBase{

    public function hookAction() {
        $event = json_decode(file_get_contents("php://input"));

        //若返回状态码不是 2xx，Ping++ 服务器会在 25 小时内向你的服务器进行多次发送，最多 8 次
        // 对异步通知做处理
        if (!isset($event->type)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
            exit("fail");
        }
        switch ($event->type) {
            case "charge.succeeded":
                // 开发者在此处加入对支付异步通知的处理代码
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                break;
            case "refund.succeeded":
                // 开发者在此处加入对退款异步通知的处理代码
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                break;
            default:
                header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
                break;
        }
        return $this->output();
    }

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
}
