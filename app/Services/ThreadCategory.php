<?php namespace App\Services;
use App\Models\ThreadCategory as mThreadCategory;

class ThreadCategory extends ServiceBase{

    public static function addNormalThreadCategory( $uid, $target_type, $target_id) {
        return self::addCategoryToThread( $uid, $target_type, $target_id, mThreadCategory::CATEGORY_TYPE_NORMAL);
    }

    public static function addCategoryToThread( $uid, $target_type, $target_id, $category_id, $status = mThreadCategory::STATUS_CHECKED ){
        if( !is_array( $category_id ) ){
            $category_id = [$category_id];
        }
        foreach( $category_id as $cat ){
            $threadCategory = new mThreadCategory();
            $threadCategory->assign([
                'create_by' => $uid,
                'target_type' => $target_type,
                'target_id' => $target_id,
                'category_id' => $cat,
                'status' => $status
            ])
            ->save();
        }
        return  $threadCategory;
    }

    public static function setCategory( $uid, $target_type, $target_id, $category_id, $status ){
        $mThreadCategory = new mThreadCategory();

        switch( $status ){
            case 'checked':
                $status = mThreadCategory::STATUS_CHECKED;
                break;
            case 'normal':
                $status = mThreadCategory::STATUS_NORMAL;
                break;
            default:
                break;
        }

        $thrdCat = $mThreadCategory->set_category( $uid, $target_type, $target_id, $category_id, $status );
        return $thrdCat;
    }

    public static function getCategoriesByTarget( $target_type, $target_id ){
        $mThreadCategory = new mThreadCategory();

        $results = $mThreadCategory->get_category_ids_of_thread( $target_type, $target_id );

        return $results;
    }
    public static function getCategoryByTarget( $target_type, $target_id, $category_id ){
        $mThreadCategory = new mThreadCategory();

        $results = $mThreadCategory->get_category_ids_of_thread( $target_type, $target_id, $category_id );
        if( $results->isEmpty() ){
            return [];
        }

        return $results[0];
    }
    public static function checkedThreadAsCategoryType( $target_type, $target_id, $category_id ){
        $cond = [
            'target_id' => $target_id,
            'target_type' => $target_type,
            'category_id' => $category_id
        ];
        return (new mThreadCategory)->where( $cond )
            ->where('status', '!=', mThreadCategory::STATUS_DELETED )
            ->exists();
    }
    public static function checkedThreadAsPopular( $target_type, $target_id ){
        return self::checkedThreadAsCategoryType( $target_type, $target_id, mThreadCategory::CATEGORY_TYPE_POPULAR );
    }

    public static function checkThreadIsPopular( $target_type, $target_id ){
        $cond = [
            'target_id' => $target_id,
            'target_type' => $target_type,
            'category_id' => mThreadCategory::CATEGORY_TYPE_POPULAR,
            'status' => mThreadCategory::STATUS_NORMAL
        ];
        return (new mThreadCategory)->where( $cond )->exists();
    }

    public static function setThreadStatus( $uid, $target_type, $target_id, $status, $reason = '', $category_id = null ){
        $mThreadCategory = new mThreadCategory();
        $thrdCat = $mThreadCategory->set_thread_status( $uid, $target_type, $target_id, $status, $reason, $category_id );
        return $thrdCat;
    }

    public static function getValidThreadsByCategoryId( $category_id, $page = '1' , $size = '15' ){
        $mThreadCategory = new mThreadCategory();
        return $mThreadCategory->get_valid_threads_by_category( $category_id, $page, $size );
    }

    public static function getCheckedThreads( $category_id, $page = '1' , $size = '15' ){
        $mThreadCategory = new mThreadCategory();
        return $mThreadCategory->get_checked_threads( $category_id, $page , $size );
    }

    public static function getPopularThreads( $type, $page = '1' , $size = '15' ){
        $mThreadCategory = new mThreadCategory();
        if( $type == 'app' ){
            $category_id     = mThreadCategory::CATEGORY_TYPE_APP_POPULAR;
        }
        else if( $type == 'pc' ){
            $category_id     = mThreadCategory::CATEGORY_TYPE_PC_POPULAR;
        }
        else{
            $category_id = mThreadCategory::CATEGORY_TYPE_POPULAR;
        }
        return $mThreadCategory->get_valid_threads_by_category( $category_id, $page , $size );
    }

    public static function deleteThread( $uid, $target_type, $target_id, $status, $reason = '', $category_id ){
        $mThreadCategory = new mThreadCategory();
        $thrdCat = $mThreadCategory->delete_thread( $uid, $target_type, $target_id, $status, $reason, $category_id );
        return $thrdCat;
    }

    public static function getAsksByCategoryId( $category_id, $status, $page, $size ){
        $mThreadCategory = new mThreadCategory();
        $threadIds = $mThreadCategory->get_asks_by_category( $category_id, $status, $page, $size );
        return $threadIds;
    }
    public static function getRepliesByCategoryId( $category_id, $page, $size ){
        $mThreadCategory = new mThreadCategory();
        $threadIds = $mThreadCategory->get_valid_replies_by_category( $category_id, $page, $size );
        return $threadIds;
    }

    /**
     * 获取活动用的隐藏的求助内容
     */
    public static function getThreadsByCategoryId( $category_id ){
        $mThreadCategory = new mThreadCategory();
        return $mThreadCategory->get_threads_by_category_id($category_id);
    }

    public static function brief( $tc ){
        if( !$tc ){
            return [
                'category_id' => 0,
                'status'      => 0,
                'target_type' => 0,
                'target_id'   => 0
            ];
        }
        return [
            'category_id' => $tc['category_id'],
            'status'      => $tc['status'],
            'target_type' => $tc['target_type'],
            'target_id'   => $tc['target_id']
        ];
    }
}
