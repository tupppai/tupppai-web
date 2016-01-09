<?php namespace App\Counters;

use App\Models\Reply as mReply;
use DB;

class ReplyClicks extends CounterBase {

    public static $key  = 'reply_clicks_';
    
    public static function _key($reply_id) {
        $key = self::$key . $reply_id;

        return $key;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($reply_id) {
        $key = self::_key($reply_id);

        return self::query($key, function() use ($key, $reply_id) {
            $mReply   = new mReply;
            $ask    = $mReply->find($reply_id);
            $count  = $ask->click_count;

            return self::put($key, $count);
        });
    }

    public static function inc($reply_id, $val = 1) {
        $count = self::get($reply_id);

        //todo 这里可以做uid筛选,一个人只算一次点击数
        //todo 50次存一次db
        if($count % 50 == 0) {
            $ask = mReply::find($reply_id);
            $ask->click_count = $count;
            $ask->save();
        }

        return self::increment(self::_key($reply_id), $val);
    }
}
