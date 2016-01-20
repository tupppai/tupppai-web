<?php namespace App\Services;

use App\Models\ThreadTag as mThreadTag;

class ThreadTag extends ServiceBase{

    public static function addTagToThread( $uid, $target_type, $target_id, $tag_id ){
        $threadTag = new mThreadTag();
        $threadTag->assign([
            'create_by' => $uid,
            'target_type' => $target_type,
            'target_id' => $target_id,
            'tag_id' => $tag_id,
            'status' => mThreadTag::STATUS_NORMAL
        ])
        ->save();
        return  $threadTag;
    }

    public static function setTag( $uid, $target_type, $target_id, $tag_id, $status ){
        $mThreadTag = new mThreadTag();

        $thrdCat = $mThreadTag->set_tag( $uid, $target_type, $target_id, $tag_id, $status );
        return $thrdCat;
    }

    public static function getTagsByTarget( $target_type, $target_id ){
        $mThreadTag = new mThreadTag();

        $results = $mThreadTag->get_tag_ids_of_thread( $target_type, $target_id, NULL );

        return $results;
    }
    public static function getTagByTarget( $target_type, $target_id, $tag_id ){
        $mThreadTag = new mThreadTag();

        $results = $mThreadTag->get_tag_ids_of_thread( $target_type, $target_id, $tag_id );
        if( $results->isEmpty() ){
            return [];
        }

        return $results[0];
    }

    public static function checkThreadHasTag( $target_type, $target_id, $tag_id){
        $cond = [
            'target_id' => $target_id,
            'target_type' => $target_type,
            'tag_id' => $tag_id,
            'status' => mThreadTag::STATUS_NORMAL
        ];
        return (new mThreadTag)->where( $cond )->exists();
    }

    //暂时没用
    public static function setThreadStatus( $uid, $target_type, $target_id, $status, $reason = '', $tag_id = null ){

        return (new mThreadTag)->set_tag($uid, $target_type, $target_id, $tag_id, $status, $reason);
    }

    public static function deleteThread( $uid, $target_type, $target_id, $status, $reason = '', $tag_id ){
        $mThreadTag = new mThreadTag();
        $thrdCat = $mThreadTag->delete_thread( $uid, $target_type, $target_id, $status, $reason, $tag_id );
        return $thrdCat;
    }

    public static function getAsksByTagId( $tag_id, $status, $page, $size ){
        $mThreadTag = new mThreadTag();
        $threadIds = $mThreadTag->get_asks_by_tag( $tag_id, $status, $page, $size );
        return $threadIds;
    }
    public static function getRepliesByTagId( $tag_id, $page, $size ){
        $mThreadTag = new mThreadTag();
        $threadIds = $mThreadTag->get_valid_replies_by_tag( $tag_id, $page, $size );
        return $threadIds;
    }

    public static function brief( $tc ){
        if( !$tc ){
            return [
                'tag_id' => 0,
                'status'      => 0,
                'target_type' => 0,
                'target_id'   => 0
            ];
        }
        return [
            'tag_id' => $tc['tag_id'],
            'status'      => $tc['status'],
            'target_type' => $tc['target_type'],
            'target_id'   => $tc['target_id']
        ];
    }
}
