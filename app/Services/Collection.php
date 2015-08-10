<?php

namespace App\Services;

use \App\Models\Collection as mCollection,
    \App\Models\Reply as mReply;

class Collection extends ServiceBase
{
    /**
     * 添加新关注
     */
    public static function addNewCollection($uid, $reply_id, $status=mCollection::STATUS_NORMAL){
        $collect = new mCollection();
        //todo: actionLog
        $hasCollected = self::getCollectionByUidAndRid( $uid, $reply_id );
        if( $hasCollected ){
            $collection = $collect->where(array('uid'=>$uid, 'reply_id'=>$reply_id))->first();
            $collection->status = $status;
        }
        else{
            $collection = $collect;
            $collection->assign(array(
                'uid'=>$uid,
                'reply_id'=>$reply_id,
                'status' => $status
            ));
        }

        return $collection->save();
    }

    public static function getCollectionByUidAndRid( $uid, $rid ){
        $collect = new mCollection();
        return $collect->where(array(
            'uid'=>$uid,
            'reply_id'=>$rid
        ))->first();
    }

    public static function collectReply($uid, $reply_id, $status) {
        $mReply = new mReply;
        $mCollection = new mCollection;

        if( !$mReply->get_reply_by_id($reply_id) )
            return error('REPLY_NOT_EXIST');

        $collect = $mCollection->has_collected_reply($uid, $reply_id);

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
        $collect->save();

        return $focus;
    }

    public static function hasCollectedReply( $uid, $reply_id ){

        $collection = (new mCollection)->has_collected_reply($uid, $reply_id);

        return $collection? true: false;
    }

    /**
     * 获取用户收藏作品数量
     */
    public static function getUserCollectionCount ( $uid ) {
        return (new mCollection)->count_user_collection($uid);
    }

    //弃用
    //public static function collection($uid, $rid, $status){
    //public static function setCollection($uid, $rid, $status)
    //public static function get_user_collection($uid, $page, $limit){
    //public static function checkUserReplyCollection( $uid, $target_id ){
}
