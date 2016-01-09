<?php namespace App\Counters;

use App\Models\Count as mCount;
use App\Services\Count as sCount;
use DB;

class ReplyShares extends CounterBase {

    public static $key  = 'reply_shares_';
    
    public static function _key($reply_id) {
        $key = self::$key . $reply_id ;

        return $key;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($reply_id) {
        $key = self::_key($reply_id);

        return self::query($key, function() use ($key, $reply_id) {
            $mCount   = new mCount;
            $count    = $mCount->where('action', sCount::ACTION_SHARE)
                ->where('target_id', $reply_id)
                ->where('type', mCount::TYPE_REPLY)
                ->valid()
                ->count();

            return self::put($key, $count);
        });
    }

    public static function inc($reply_id, $val = 1) {
        self::get($reply_id);

        return self::increment(self::_key($reply_id), $val);
    }
}
