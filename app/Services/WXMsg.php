<?php

namespace App\Services;
use App\Jobs\SendWxMsg as jSendWxMsg;
use Queue;

class WXMsg extends ServiceBase{
	// 图片处理完成
    const TPL_ID_REQUEST_SUCCESS    = '3ZVOGRhdYT61rvOX62R-OfxxIHXMvxJJbrBhhqkBRL4';
    const TPL_ID_REQUEST_REJECT     = '3ZVOGRhdYT61rvOX62R-OfxxIHXMvxJJbrBhhqkBRL4';

    public static function sendMsg( $tplId, $vars, $openIDs, $url = '' ){
        $url = env('APP_URL', 'http://film.tupppai.com/') . $url;
        if( !is_array( $openIDs ) ){
            $openIDs = [$openIDs];
        }

        foreach( $openIDs as $openid ){
			Queue::push( new jSendWxMsg( $tplId, $vars, $openid, $url ) );
        }
        return true;
	}

}
