<?php namespace App\Counters;

use App\Models\Ask as mAsk;
use App\Models\Download as mDownload;
use App\Services\Download as sDownload;
use DB;

class UserDownloadAsks extends CounterBase {

    public static $key  = 'user_download_asks_';
    
    public static function _key($uid) {
        return $key = self::$key . $uid;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($uid) {
        $key = self::_key($uid);

        return self::query($key, function() use ($key, $uid) {
            $mDownload = new mDownload;
            $mAsk      = new mAsk;

            $ask_table      = $mAsk->getTable();
            $download_table = $mDownload->getTable();

            $count  = $mAsk->whereIn("$ask_table.id", function($query) use ($download_table, $uid) {
                    $query->from($download_table)
                        ->select("$download_table.target_id")
                        ->distinct()
                        ->where("$download_table.uid", $uid)
                        ->where("$download_table.type", mDownload::TYPE_ASK)
                        ->where("$download_table.status", '>', mDownload::STATUS_DELETED);
                })
                ->valid()
                ->blocking($uid)
                ->count();

            return self::put($key, $count);
        });
    }
    
    public static function inc($uid, $val = 1) {
        self::get($uid);

        return self::increment(self::_key($uid), $val);
    }
}
