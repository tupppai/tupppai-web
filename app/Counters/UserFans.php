<?php namespace App\Counters;

use App\Models\Follow as mFollow;
use DB;

class UserFans extends CounterBase {

    public static $key  = 'user_fans_';
    
    public static function _key($uid) {
        return self::$key . $uid;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($uid) {
        $key = self::_key($uid);

        return self::query($key, function() use ($key, $uid) {
            $mFan = new mFollow;
            $count  = $mFan->where('follow_who', $uid)
                ->valid()
                ->count();

            return self::put($key, $count);
        });
    }
    
    public static function inc($uid, $val = 1) {
        self::get($uid);

        return self::increment(self::_key($uid, $val));
    }
}
