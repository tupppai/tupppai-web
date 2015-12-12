<?php namespace App\Counters;

use App\Models\Comment as mComment;
use DB;

class ReplyComments extends CounterBase {

    public static $key  = 'reply_comments_';
    
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
            $mComment = new mComment;
            $count    = $mComment->where('type', mComment::TYPE_REPLY)
                ->where('target_id', $reply_id)
                ->valid()
                //->blocking($uid)
                ->count();

            return self::put($key, $count);
        });
    }
    
    public static function inc($reply_id, $val = 1) {
        self::get($reply_id);

        return self::increment(self::_key($reply_id), $val);
    }
}
