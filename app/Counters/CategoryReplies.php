<?php namespace App\Counters;

use App\Models\ThreadCategory as mThreadCategory;
use App\Services\ThreadCategory as sThreadCategory;
use DB;

class CategoryReplies extends CounterBase {

    public static $key  = 'category_replies_';
    
    public static function _key($category_id) {
        return $key = self::$key . $category_id;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($category_id) {
        $key = self::_key($category_id);

        return self::query($key, function() use ($key, $category_id) {

            $mThreadCategory = new mThreadCategory;
            $count = mThreadCategory::where('category_id', $category_id)
                ->where('target_type', mThreadCategory::TYPE_REPLY)
                ->count();

            return self::put($key, $count);
        });
    }
    
    public static function inc($type, $id, $val = 1) {
        //if($type == TYPE_CATEGORY)
        $count = 0;

        $categories = sThreadCategory::getCategoriesByTarget( $type, $id);
        foreach($categories as $category) {
            self::get($category->id);
            $count += self::increment(self::_key($category->id), $val);
        }

        return $count;
    }
}