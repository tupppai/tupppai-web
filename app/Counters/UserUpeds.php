<?php namespace App\Counters;

use App\Models\Count as mCount;
use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;
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
            $mAsk   = new mAsk;
            $mReply = new mReply;
            $count_table = $mCount->getTable();
            $reply_table = $mReply->getTable();
            $ask_table   = $mAsk->getTable();

            $ask_count   = 0;
            /*
                $mCount->where("$count_table.type", mAsk::TYPE_ASK)
                ->whereIn("$count_table.target_id", function($query) use ($ask_table, $uid) {
                    $query->from($ask_table)
                        ->select("$ask_table.id")
                        ->where("$ask_table.uid", $uid)
                        ->get();
                })->count();
             */

            $reply_count   = $mCount->where("$count_table.type", mReply::TYPE_REPLY)
                ->where("$count_table.action", sCount::ACTION_UP)
                ->where("$count_table.status", '>', mReply::STATUS_DELETED)
                ->whereIn("$count_table.target_id", function($query) use ($reply_table, $uid) {
                    $query->from($reply_table)
                        ->select("$reply_table.id")
                        ->where("$reply_table.uid", $uid)
                        ->where("$reply_table.status", ">", mReply::STATUS_DELETED)
                        ->get();
            })->count();

            return self::put($key, $ask_count + $reply_count);
        });
    }
    
    public static function inc($uid, $val = 1) {
        self::get($uid);

        return self::increment(self::_key($uid), $val);
    }
}
