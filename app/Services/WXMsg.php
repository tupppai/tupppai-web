<?php

namespace App\Services;
use App\Jobs\SendWxMsg as jSendWxMsg;
use Queue;

class WXMsg extends ServiceBase{
	// 图片处理完成
    const TPL_ID_REQUEST_SUCCESS    = 'KU10CDeqLKxZhxBati6FXi9nU9NGFyuqlBCuWG7FNNA';
    const TPL_ID_REQUEST_REJECT     = 'sHBKMglO5LjfrlNdyWjc9A2dIEb8ZcWP2m16Bw8IRSI';
    const TPL_ID_HAS_NEW_REPLY      = 'XVkNiJZLfzRvpEU3CP_kzNceVbKF3-3haJbuVI3Qr9U';

    public static function sendMsg( $tplId, $vars, $openIDs, $url = '' ){
        $url = 'http://'.env('MAIN_HOST') . $url;
        if( !is_array( $openIDs ) ){
            $openIDs = [$openIDs];
        }

        foreach( $openIDs as $openid ){
			Queue::push( new jSendWxMsg( $tplId, $vars, $openid, $url ) );
        }
        return true;
	}

}
