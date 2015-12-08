<?php
namespace App\Counters;

use Cache;

class CounterBase {

    public static $key = 'counter_';

    public static function _key($id) {
        return self::$key.$id;
    }

    /**
     * 获取计数数据,closure
     */ 
    public static function query($id, $closure = null) {
        $value = Cache::get(self::_key($id), $closure);
        return $value;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($id) {
        $value = Cache::get(self::_key($id));
    }

    /**
     * 设置计数器
     */
    public static function put($id, $val) {
        Cache::put(self::_key($id), $val, env('CACHE_LIFETIME'));
        dd($val);
        return $val;
    }

    /**
     * 自增计数器
     */
    public static function inc($id, $val = 1) {

        return Cache::increment(self::_key($id), $val);
    }

    /**
     * demo for closure
     */
    public static function getClosure($id) {

        return self::get($id, function() use ($id) {
            $value = 1;

            self::put($id, $value);
            return $value;
        });
    } 
}
