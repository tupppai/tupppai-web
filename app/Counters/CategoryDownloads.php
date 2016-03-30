<?php namespace App\Counters;

use App\Models\Download as mDownload;
use DB;

class CategoryDownloads extends CounterBase {

    public static $key  = 'category_downloads_';
    
    public static function _key($category_id) {
        return $key = self::$key . $category_id;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($category_id) {
        $key = self::_key($category_id);

        return self::query($key, function() use ($key, $category_id) {

            $mDownload  = new mDownload;
            $count      = $mDownload->where('category_id', $category_id)
                //->where('status', '>', mDownload::STATUS_NORMAL)
                ->count();

            return self::put($key, $count);
        });
    }
    
    public static function inc($category_id, $val = 1) {
        self::get($category_id);

        return self::increment(self::_key($category_id), $val);
    }
}
