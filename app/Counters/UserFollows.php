<?php namespace App\Counters;

use App\Models\Follow as mFollow;
use DB;

class UserFollows extends CounterBase {

    public static $key  = 'user_follows_';
    
    public static function _key($uid) {
        return self::$key . $uid;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($uid) {
        $key = self::_key($uid);

        return self::query($key, function() use ($key, $uid) {
            $mFollow= new mFollow;
            $count  = $mFollow->where('uid', $uid)
                ->valid()
                ->count();

            return self::put($key, $count);
        });
    }
    
    public static function inc($uid, $val = 1) {
        self::get($uid);

        return self::increment(self::_key($uid), $val);
    }
}
