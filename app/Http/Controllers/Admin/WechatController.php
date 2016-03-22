<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Services\UserLanding;
use App\Services\WxPush as sWxPush;
use App\Services\WxActGod as sWxActGod;
use App\Models\UserLanding as mUserLanding;
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

        $app->server->setMessageHandler(function($message){
          
            switch ($message->MsgType) {
                case 'event':
                    # 事件消息...
                    if($message->Event == 'subscribe'){
                        return sWxPush::followAutoReply();
                    }
                    break;
                case 'text':
                    if($message->Content == '男神'){
                        $open_id = $message->FromUserName;
                        return sWxPush::godMan($open_id);
                    }
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
