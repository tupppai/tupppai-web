<?php namespace App\Counters;

use App\Models\Reply as mReply;
use DB;

class UserReplies extends CounterBase {

    public static $key  = 'user_replies_';
    public static $block= 'blocking_';
    
    public static function _key($uid) {
        if( $uid == _uid() ) 
            $key = self::$key . self::$block . $uid;
        else 
            $key = self::$key . $uid;
        return $key;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($uid) {
        $key = self::_key($uid);

        return self::query($key, function() use ($key, $uid) {
            $mReply = new mReply;
            $count  = $mReply->where('uid', $uid)
                ->valid()
                ->blocking($uid)
                ->count();

            return self::put($key, $count);
        });
    }

    public static function inc($uid) {
        self::get($uid);

        return parent::inc(self::_key($uid));
    }
}
