<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Text;

use Log;

class WechatController extends Controller {

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serveAction()
    {
        $options = config('wechat');
        $app = new Application($options);

        Log::info('request arrived.');

        $app->server->setMessageHandler(function($message){

            switch ($message->MsgType) {
                case 'event':
                    # 事件消息...
                    break;
                case 'text':
                    Log::info('text', array($messages));
                    # 文字消息...
                    break;
                case 'image':
                    # 图片消息...
                    break;
                case 'voice':
                    # 语音消息...
                    break;
                // ... 其它消息
                default:
                    # code...
                    break;
            }
        });

        Log::info('return response.');

        return $app->server->serve();
    }
}
