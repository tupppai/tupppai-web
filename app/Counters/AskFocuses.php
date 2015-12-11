<?php namespace App\Counters;

use App\Models\Comment as mComment;
use App\Models\Focus as mFocus;
use DB;

class AskFocuses extends CounterBase {

    public static $key  = 'ask_focuses_';
    
    public static function _key($ask_id) {
        $key = self::$key . $ask_id;

        return $key;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($ask_id) {
        $key = self::_key($ask_id);

        return self::query($key, function() use ($key, $ask_id) {
            $mFocus= new mFocus;
            $count= $mFocus->where('ask_id', $ask_id)
                ->where('status', mFocus::STATUS_NORMAL)
                ->count();

            return self::put($key, $count);
        });
    }

    public static function inc($ask_id, $val = 1) {
        self::get($ask_id);

        return self::increment(self::_key($ask_id), $val);
    }
}
