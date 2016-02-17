<?php namespace App\Services;

use App\Services\Ask as sAsk;
use App\Services\Reply as sReply;
use App\Models\ThreadCategory as mThreadCategory;

use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;

class ThreadCategory extends ServiceBase{

    public static function addNormalThreadCategory( $uid, $target_type, $target_id) {
        return self::addCategoryToThread( $uid, $target_type, $target_id, mThreadCategory::CATEGORY_TYPE_NORMAL, mThreadCategory::STATUS_NORMAL);
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
            case 'done':
                $status = mThreadCategory::STATUS_DONE;
                break;
            case 'delete':
                $status = mThreadCategory::STATUS_DELETED;
                break;
            default:
                break;
        }
        if( $target_type == mThreadCategory::TYPE_ASK && $status == mThreadCategory::STATUS_NORMAL ){
            $ask = sAsk::getAskById( $target_id );
            $status = $ask->status;
        }
        else if( $target_type == mThreadCategory::TYPE_REPLY && $status == mThreadCategory::STATUS_NORMAL ){
            $reply = sReply::getReplyById( $target_id );
            $status = $reply->status;
        }

        $thrdCat = $mThreadCategory->set_category( $uid, $target_type, $target_id, $category_id, $status );
        if( $thrdCat
            && $target_type == mThreadCategory::TYPE_ASK
            && $status == mThreadCategory::STATUS_CHECKED ){

            $replies = (new mReply)->get_all_replies_by_ask_id( $target_id, 0, 0 );

            //将求助对应的所有作品都赛入频道
            foreach ($replies as $reply) {
                if( $reply->status > mThreadCategory::STATUS_DELETED ){
                    $mThreadCategory->set_category( $uid, mThreadCategory::TYPE_REPLY, $reply->id, $category_id, $status );
                }
            }
        }
        return $thrdCat;
    }

    /**
     * 获取第一版本热门频道的数据
     */
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
        $threads= $mThreadCategory->get_valid_threads_by_category( $category_id, $page , $size );

        $data   = array();
        foreach($threads as $thread) {
            $data[] = self::parse($thread->target_type, $thread->target_id);
        }
        return $data;
    }

    /**
     * 通过category_id获取频道求助数据
     */
    public static function getAsksByCategoryId( $category_id, $status, $page, $size ){
        $mThreadCategory = new mThreadCategory();
        $threadIds = $mThreadCategory->get_asks_by_category( $category_id, $status, $page, $size );
        return $threadIds;
    }

    /**
     * 通过category_id获取频道作品数据
     */
    public static function getRepliesByCategoryId( $category_id, $page, $size ){
        $mThreadCategory = new mThreadCategory();
        $threadIds = $mThreadCategory->get_valid_replies_by_category( $category_id, $page, $size );
        return $threadIds;
    }

    /**
     * 通过频道获取隐藏的求p内容
     */
    public static function getHiddenAskByCategoryId($category_id) {
        return (new mAsk)->get_hidden_ask_by_category_id($category_id);
    }

    /**
     * 通过频道id获取有作品的求助
     */
    public static function getCompletedAsksByCategoryId($category_id, $page, $size) {
        return (new mAsk)->get_completed_asks_by_category_id($category_id, $page, $size);
    }

    /**
     * 通过type和id获取category集合
     */
    public static function getCategoriesByTarget( $target_type, $target_id, $status = NULL ){
        $mThreadCategory = new mThreadCategory();

        $results = $mThreadCategory->get_category_ids_of_thread( $target_type, $target_id, NULL, $status );

        return $results;
    }

    // Except tutorial
    public static function getUsersAsk( $uid, $page, $size ){
        $mThreadCategory = new mThreadCategory();
        $tcTable = 'thread_categories';//$mThreadCategory->table;
        $ask_ids = $mThreadCategory->leftjoin('asks', function ($join) use ($tcTable, $uid) {
                                        $join->on($tcTable . '.target_id', '=', 'asks.id')
                                            ->where($tcTable . '.target_type', '=', mThreadCategory::TYPE_ASK);
                                    })
                                  ->where("uid", $uid)
                                  ->where('category_id','!=', mThreadCategory::CATEGORY_TYPE_TUTORIAL)
                                  ->blocking( $uid, 'asks' )
                                  ->distinct( 'category_id' )
                                  ->orderBy( 'category_id', 'DESC')
                                  ->orderBy( 'asks.create_time', 'DESC')
                                  ->forPage( $page, $size )
                                  ->select( 'target_id' )
                                  ->get();
        return $ask_ids;
    }




    //===========================  后台代码 ===========================

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
    //检查归属
    public static function checkThreadIsInParentCategoryOf( $target_type, $target_id, $category_id ){
        $mTG = new mThreadCategory();
        return $mTG->thread_has_parent_category_of( $target_type, $target_id, $category_id );
    }
    public static function checkThreadIsActivity( $target_type, $target_id ){
        return self::checkThreadIsInParentCategoryOf( $target_type, $target_id, mThreadCategory::CATEGORY_TYPE_ACTIVITY );
    }
    public static function checkThreadIsChannel( $target_type, $target_id ){
        return self::checkThreadIsInParentCategoryOf( $target_type, $target_id, mThreadCategory::CATEGORY_TYPE_ACTIVITY );
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
    /**
     * 获取活动用的隐藏的求助内容
     */
    public static function getThreadsByCategoryId( $category_id, $page, $size ){
        $mThreadCategory = new mThreadCategory();
        return $mThreadCategory->get_threads_by_category_id($category_id, $page, $size);
    }

    public static function setThreadStatus( $uid, $target_type, $target_id, $status, $reason = '', $category_id = null ){

        return (new mThreadCategory)->set_category($uid, $target_type, $target_id, $category_id, $status, $reason);
    }

    public static function getValidThreadsByCategoryId( $category_id, $page = '1' , $size = '15' ,$orderByThread = true ,$searchArguments = []){
        $mThreadCategory = new mThreadCategory();
        return $mThreadCategory->get_valid_threads_by_category( $category_id, $page, $size, $orderByThread , $searchArguments );
    }

    public static function getCheckedThreads( $category_id, $page = '1' , $size = '15' ,$arguments = [] ){
        $mThreadCategory = new mThreadCategory();
        return $mThreadCategory->get_checked_threads( $category_id, $page , $size ,$arguments);
    }

    /**
     * 删除频道或者活动
     */
    public static function deleteThread( $uid, $target_type, $target_id, $status, $reason = '', $category_id ){
        $mThreadCategory = new mThreadCategory();
        $thrdCat = $mThreadCategory->delete_thread( $uid, $target_type, $target_id, $status, $reason, $category_id );
        return $thrdCat;
    }

    public static function admin_brief( $tc ){
        $data = [
            'category_id' => 0,
            'status'      => 0,
            'target_type' => 0,
            'target_id'   => 0
        ];

        if($tc) foreach($data as $key=>$val) {
            $data[$key] = $tc->$key;
        }
        return $data;
    }
}
