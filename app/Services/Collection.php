<?php

namespace App\Services;

use \App\Models\Collection as mCollection,
    \App\Models\Reply as mReply;

class Collection extends ServiceBase
{
    
    public static function getCollectionByUidAndRid( $uid, $rid ){
        $collect = new mCollection();
        return $collect->where(array(
            'uid'=>$uid,
            'reply_id'=>$rid
        ))->first();
    }

    public static function getReplyIdsByUid( $uid, $last_read_time, $page, $size ){
        $collect = new mCollection();
        return $collect->where( 'uid', $uid ) -> where('update_time','<', $last_read_time)->get('GROUP_CONCAT(reply_id)');
    }

    public static function collectReply($uid, $reply_id, $status) {
        $mReply = new mReply;
        $mCollection = new mCollection;

        if( !$mReply->get_reply_by_id($reply_id) )
            return error('REPLY_NOT_EXIST');

        $cond = [
            'uid' => $uid,
            'reply_id' => $reply_id
        ];
        $collect = $mCollection->firstOrNew( $cond );

        $data = $cond;
        if( !$collect->id ){
            if( $status = mCollection::STATUS_DELETED ){
                return true;
            }
           $data['create_time'] = time(); 
        }
        $data['update_time'] = time();
        $data['status'] = $status;
        $collect->fill($data)->save();

        return $collect;
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
