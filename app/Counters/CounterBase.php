<?php
namespace App\Counters;

use Cache;

class CounterBase {

    public static $key = 'counter_';

    public static function __key($id) {
        return self::$key.$id;
    }

    /**
     * 获取计数数据,closure
     */ 
    public static function query($id, $closure = null) {
        $value = Cache::get(self::__key($id), $closure);
        return $value;
    }

    /**
     * 获取计数数据
    public static function get($id) {
        $value = Cache::get(self::__key($id));
    }
     */ 

    /**
     * 设置计数器
     */
    public static function put($id, $val) {
        Cache::put(self::__key($id), $val, env('CACHE_LIFETIME'));
        return $val;
    }

    /**
     * 自增计数器
     */
    public static function increment($id, $val = 1) {

        return Cache::increment(self::__key($id), $val);
    }
}
