<?php namespace App\Counters;

use App\Models\Comment as mComment;
use DB;

class AskComments extends CounterBase {

    public static $key  = 'ask_comments_';
    
    public static function _key($ask_id, $uid) {
        $key = self::$key . $ask_id . '_uid_' . $uid;

        return $key;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($ask_id, $uid) {
        $key = self::_key($ask_id, $uid);

        return self::query($key, function() use ($key, $ask_id, $uid) {
            $mComment = new mComment;
            $count    = $mComment->where('type', mComment::TYPE_ASK)
                ->where('target_id', $ask_id)
                ->valid()
                ->blocking($uid)
                ->count();

            return self::put($key, $count);
        });
    }

    public static function inc($ask_id, $uid, $val = 1) {
        self::get($ask_id, $uid);

        return self::increment(self::_key($ask_id, $uid));
    }
}
