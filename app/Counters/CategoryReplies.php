<?php namespace App\Counters;

use App\Models\ThreadCategory as mThreadCategory;
use App\Models\Reply as mReply;
use App\Services\ThreadCategory as sThreadCategory;


use DB;

class CategoryReplies extends CounterBase {

    public static $key  = 'category_replies_';
    
    public static function _key($ask_id) {
        $uid = _uid();
        $key = self::$key . $ask_id . '_uid_' . $uid;

        return $key;
    }
    
    /**
     * 获取计数数据
     */ 
    public static function get($category_id) {
        $uid = _uid();
        $key = self::_key($category_id, $uid);

        return self::query($key, function() use ($key, $category_id, $uid) {

            $mThreadCategory = new mThreadCategory;
            $mReply = new mReply;
            $reply_table = $mReply->getTable();
            $count = $mReply->valid()
                ->blocking($uid)
                ->whereIn($reply_table.'.id', function($query) use ($category_id) {
                    $query->from('thread_categories')
                        ->where('category_id', $category_id)
                        ->where('target_type', mThreadCategory::TYPE_REPLY)
                        ->where('status', '>=', mThreadCategory::STATUS_NORMAL)
                        ->select('target_id');
                })
                ->count();
            /*
            $count = $mThreadCategory->where('status', '>', mThreadCategory::STATUS_DELETED)
                ->where('category_id', $category_id)
                ->where('target_type', mThreadCategory::TYPE_REPLY)
                ->count();
            */
             

            return self::put($key, $count);
        });
    }
    
    public static function inc($type, $id, $val = 1) {
        $uid = _uid();
        //if($type == TYPE_CATEGORY)
        $count = 0;

        $categories = sThreadCategory::getCategoriesByTarget( $type, $id);
        foreach($categories as $category) {
            self::get($category->id, $uid); 
            //self::get($category->id);
            $count += self::increment(self::_key($category->id), $val);
        }

        return $count;
    }
}
