<?php namespace App\Counters;

use App\Models\Collection as mCollection;
use DB;

class ReplyCollections extends CounterBase {

    public static $key  = 'reply_collections_';
    
    public static function _key($reply_id) {
        $key = self::$key . $reply_id;

        return $key;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($reply_id) {
        $key = self::_key($reply_id);

        return self::query($key, function() use ($key, $reply_id) {
            $mCollection = new mCollection;
            $count  = $mCollection->where('reply_id', $reply_id)
                ->where('status', mCollection::STATUS_NORMAL)
                ->count();

            return self::put($key, $count);
        });
    }

    public static function inc($reply_id, $val = 1) {
        self::get($reply_id);

        return self::increment(self::_key($reply_id), $val);
    }
}
