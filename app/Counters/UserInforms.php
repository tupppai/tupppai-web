<?php namespace App\Counters;

use App\Models\Inform as mInform;
use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;
use App\Models\Comment as mComment;
use DB;

class UserInforms extends CounterBase {

    public static $key  = 'user_informs_';
    
    public static function _key($uid) {
        $key = self::$key . $uid;
        return $key;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($uid) {
        $key = self::_key($uid);

        return self::query($key, function() use ($key, $uid) {
            $mAsk   = new mAsk;
            $mReply = new mReply;
            $mComment   = new mComment;

            $ask_table  = $mAsk->getTable();
            $reply_table= $mReply->getTable();
            $comment_table  = $mComment->getTable();

            $mInform = new mInform;
            $inform_table = $mInform->getTable();

            $ask_count = $mInform->whereIn("$inform_table.id", function($query) use ($ask_table, $uid) {
                    $query->from($ask_table)
                        ->select("$ask_table.id")
                        ->distinct()
                        ->where("$ask_table.uid", $uid);
                })
                ->count();

            $reply_count = $mInform->whereIn("$inform_table.id", function($query) use ($reply_table, $uid) {
                    $query->from($reply_table)
                        ->select("$reply_table.id")
                        ->distinct()
                        ->where("$reply_table.uid", $uid);
                })
                ->count();

            $comment_count = $mInform->whereIn("$inform_table.id", function($query) use ($comment_table, $uid) {
                    $query->from($comment_table)
                        ->select("$comment_table.id")
                        ->distinct()
                        ->where("$comment_table.uid", $uid);
                })
                ->count();

            $inform_count = $ask_count + $reply_count + $comment_count;

            return self::put($key, $inform_count);
        });
    }
    
    public static function inc($uid, $val = 1) {
        self::get($uid);

        return self::increment(self::_key($uid), $val);
    }
}
