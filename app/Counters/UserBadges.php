<?php namespace App\Counters;

use DB;
use Log;

class UserBadges extends CounterBase {

    public static $key  = 'user_badges_';
    
    public static function _key($uid) {
        return $key = self::$key . $uid;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($uid) {
        $key = self::_key($uid);

        return self::query($key, function() {
            return 0;
        });
    }

    public static function reset($uid) {
        self::get($uid);

        self::put(self::_key($uid), 0);
    }
    
    public static function inc($uid, $val = 1) {
        self::get($uid);

        return self::increment(self::_key($uid), $val);
    }
}
