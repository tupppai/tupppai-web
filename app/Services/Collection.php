<?php

namespace App\Services;

use App\Models\Collection as mCollection,
    App\Models\Reply as mReply;

use App\Services\ActionLog as sActionLog;
use App\Counters\ReplyCollections as cReplyCollections;

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
        #sky where写到model，写到scope？
        return $collect->where( 'uid', $uid ) -> where('update_time','<', $last_read_time)->get('GROUP_CONCAT(reply_id)');
    }

    public static function collectReply($uid, $reply_id, $status) {
        $mReply = new mReply;
        $mCollection = new mCollection;

        $reply = $mReply->get_reply_by_id($reply_id);
        if( !$reply ) {
            return error('REPLY_NOT_EXIST');
        }

        $collect = $mCollection->get_collection($uid, $reply_id);
        sActionLog::init( 'COLLECT_REPLY', $collect );

        if(!$collect) {
            $collect = new mCollection;
        }
        else if($collect && $collect->status == $status){
            return $collect;
        } 

        if($status == mCollection::STATUS_NORMAL) {
            cReplyCollections::inc($reply->id);
        }
        else {
            cReplyCollections::inc($reply->id, -1);
        }

        $collect->assign(array(
            'uid' => $uid,
            'reply_id' => $reply_id,
            'status'=>$status
        ));
        $collect->save();    
        sActionLog::save( $collect );

        return $collect;
    }

    public static function hasCollectedReply( $uid, $reply_id ){

        $collection = (new mCollection)->has_collected_reply($uid, $reply_id);

        return $collection? true: false;
    }
}
