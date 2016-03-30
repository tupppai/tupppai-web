<?php namespace App\Counters;

use App\Models\Ask as mAsk;
use DB;

class AskClicks extends CounterBase {

    public static $key  = 'ask_clicks_';
    
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
            $mAsk   = new mAsk;
            $ask    = $mAsk->find($ask_id);
            $count  = $ask->click_count;

            return self::put($key, $count);
        });
    }

    public static function inc($ask_id, $val = 1) {
        $count = self::get($ask_id);

        //todo 这里可以做uid筛选,一个人只算一次点击数
        //todo 50次存一次db
        if($count % 50 == 0) {
            $ask = mAsk::find($ask_id);
            $ask->click_count = $count;
            $ask->save();
        }

        return self::increment(self::_key($ask_id), $val);
    }
}
