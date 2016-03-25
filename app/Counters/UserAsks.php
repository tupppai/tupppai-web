<?php namespace App\Counters;

use App\Models\Ask as mAsk;
use DB;

class UserAsks extends CounterBase {

    public static $key  = 'user_asks_';
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
            $mAsk   = new mAsk;
            $count  = $mAsk->where('uid', $uid)
                ->valid()
                ->blocking($uid)
                ->count();

            return self::put($key, $count);
        });
    }
    
    public static function inc($uid, $val = 1) {
        self::get($uid);

        return self::increment(self::_key($uid), $val);
    }
}
