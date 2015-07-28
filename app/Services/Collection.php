<?php

namespace App\Services;

use \App\Models\Collection as mCollection;

class Collection extends ServiceBase
{
    /**
     * 添加新关注
     */
    public static function addNewCollection($uid, $aid, $status){
        $focus = new self();
        $focus->uid = $uid;
        $focus->ask_id = $aid;
        $focus->create_time = time();
        $focus->update_time = time();
        $focus->status = $status;

        return $focus->save_and_return($focus);
    }

    public static function collectReply($uid, $reply_id, $status) {

        if( !mReply::findFirst($target_id) )
            return error('REPLY_NOT_EXIST');

        $collect = mCollection::findFirst(
            " uid = {$uid} ".
            " AND reply_id = {$target_id} "
        );
        if( !$collect ) {
            return self::addNewCollection(
                $uid,
                $reply_id,
                $status
            ) ;
        }

        if($collect->status == $status) {
            return $collect;
        }

        $collect->status = $status;
        $collect = $collect->save_and_return($collect);

        return $focus;
    }

    public static function hasCollectedReply( $uid, $reply_id ){
        $collection = mCollection::findFirst(
            'reply_id=' . $reply_id .
            ' AND status=' . mCollection::STATUS_NORMAL .
            ' AND uid='.$uid
        );

        return $collection? true: false;
    }

    /**
     * 获取用户收藏作品数量
     */
    public static function getUserCollectionCount ( $uid ) {
        return mCollection::count(array("uid = {$uid} AND status = ".mCollection::STATUS_NORMAL));
    }

    //弃用
    //public static function collection($uid, $rid, $status){
    //public static function setCollection($uid, $rid, $status)
    //public static function get_user_collection($uid, $page, $limit){
    //public static function checkUserReplyCollection( $uid, $target_id ){
}
