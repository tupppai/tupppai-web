<?php namespace App\Http\Controllers\Api;

use Log;

class HookController extends ControllerBase{

    public function loginAction() {
        //todo: zhibo token set for login
        return $this->output();
    }

    public function indexAction() {
        $str   = file_get_contents("php://input");
        Log::info('php://input', array($str));

        $event = json_decode($str);

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
}
