<?php namespace App\Counters;

use App\Models\Count as mCount;
use App\Services\Count as sCount;
use DB;

class UserUpeds extends CounterBase {

    public static $key  = 'user_upeds_';
    
    public static function _key($uid) {
        return $key = self::$key . $uid;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($uid) {
        $key = self::_key($uid);

        return self::query($key, function() use ($key, $uid) {
            $mCount = new mCount;
            $count  = $mCount->where('uid', $uid)
                ->where('action', sCount::ACTION_UP)
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
