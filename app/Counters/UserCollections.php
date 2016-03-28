<?php namespace App\Counters;

use App\Models\Focus as mFocus;
use App\Models\Collection as mCollection;
use DB;

class UserCollections extends CounterBase {

    public static $key  = 'user_collections_';
    public static $block= 'blocking_';
    
    public static function _key($uid) {
        if( $uid == _uid() ) 
            $key = self::$key . self::$block . $uid;
        else 
            $key = self::$key . $uid;
        return $key;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($uid) {
        $key = self::_key($uid);
        
        return self::query($key, function() use ($key, $uid) {
            $mFocus = new mFocus;
            $mCollection = new mCollection;

            $focus_count = $mFocus->where('uid', $uid)
                ->valid()
                ->blocking($uid)
                ->count();

            $collection_count = $mCollection->where('uid', $uid)
                ->valid()
                ->blocking($uid)
                ->count();

            $count = $focus_count + $collection_count;

            return self::put($key, $count);
        });
    }
    
    public static function inc($uid, $val = 1) {
        self::get($uid);

        return self::increment(self::_key($uid), $val);
    }
}
