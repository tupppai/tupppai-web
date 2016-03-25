<?php namespace App\Counters;

use App\Models\Download as mDownload;
use DB;

class AskDownloads extends CounterBase {

    public static $key  = 'ask_downloads_';
    
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
            $mDownload = new mDownload;
            $download  = $mDownload->where('type', mDownload::TYPE_ASK)
                ->where('target_id', $ask_id);

            $count = $download->count();

            return self::put($key, $count);
        });
    }

    //todo: add status
    public static function inc($ask_id, $val = 1) {
        self::get($ask_id); 

        return self::increment(self::_key($ask_id), $val);
    }
}
