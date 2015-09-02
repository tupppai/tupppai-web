<?php

namespace App\Services;

use DB;

use App\Models\Comment as mComment,
    App\Models\Count  as mCount,
    App\Models\Ask as mAsk,
    App\Models\Reply as mReply,
    App\Models\Usermeta as mUsermeta;

use App\Services\Count as sCount,
    App\Services\Ask as sAsk,
    App\Services\Usermeta as sUsermeta,
    App\Services\Reply as sReply,
    App\Services\Message as sMessage,
    App\Services\ActionLog as sActionLog;

use Queue, App\Jobs\Push;

class Comment extends ServiceBase
{

    /**
     * Add new comment
     * @param integer $uid                  用户ID
     * @param string  $content              评论内容
     * @param integer $type         评论类型
     * @param integer $target_id    评论目标ID
     * @param integer $reply_to     评论回复评论目标ID 默认为0
     * @return $new_id              新创建评论ID
     */
    public static function addNewComment($uid, $content, $type, $target_id, $reply_to=0, $for_comment = 0) {
        $mAsk   = new mAsk;
        $mReply = new mReply;
        $mComment = new mComment;
        $msg_type   = 'comment';
        switch( $type ){
            case mComment::TYPE_ASK:
                $target     = $mAsk->get_ask_by_id($target_id);
                $reply_to   = $target->uid;
                $msg_type       = 'comment_ask';
                break;
            case mComment::TYPE_REPLY:
                $target     = $mReply->get_reply_by_id($target_id);
                $reply_to   = $target->uid;
                $msg_type       = 'comment_reply';
                break;
            case mComment::TYPE_COMMENT:
                $target     = $mComment->get_comment_by_id($for_comment);
                $reply_to   = $target->uid;
                $msg_type       = 'comment_comment';
                break;
            default:
                $reply_to = 0;
        }
        if ( !$target ) {
            return error('COMMENT_ERR');
        }
        $comment = new mComment();
        sActionLog::init('POST_COMMENT', $comment);

        $comment->assign(array(
            'uid'         => $uid,
            'content'     => $content,
            'type'        => $type,
            'target_id'   => $target_id,
            'reply_to'    => $reply_to,
            'for_comment' => $for_comment
        ));

        $comment->save();
        
        #评论推送
        Queue::push(new Push(array(
            'uid'=>$reply_to,
            'type'=>$msg_type,
            'comment_id'=>$comment->id,
            'for_comment'=> !$for_comment?$for_comment:0
        )));
        sActionLog::save($comment);

        switch( $type ){
            case mComment::TYPE_REPLY:
                sReply::updateReplyCount ($target_id, 'comment', mCount::STATUS_NORMAL);
                break;
            case mComment::TYPE_ASK:
                sAsk::updateAskCount ($target_id, 'comment', mCount::STATUS_NORMAL);
                break;
            default:
                break;
        }
        return $comment;
    }

    /**
     * 获取评论列表
     * todo: redis sort
     */
    public static function getComments($type, $target_id, $page=1, $size=10) {
        $FIRST_PAGE_HOT_COMMENT_SIZE = 3; //todo save as configuration file
        $mComment = new mComment;
        // comment 评论
        $data = array();

        $hotComments = new mComment();
        $hotComments = $hotComments->getHotComments( $type, $target_id, $page, $FIRST_PAGE_HOT_COMMENT_SIZE );

        $comment_arr = array();
        foreach ($hotComments as $comment) {
            $comment_arr[] = self::detail($comment);
        }
        $data['hot_comments'] = $comment_arr;


        $newComments = new mComment();
        $newComments = $newComments->getNewComments( $type, $target_id, $page, $size );

        $comment_arr = array();
        foreach ($newComments as $comment) {
            $comment_arr[] = self::detail($comment);
        }
        $data['new_comments'] = $comment_arr;

        return $data;
    }

    /**
     * 数量变更
     */
    public static function updateCommentCount( $id, $count_name, $status){
        $count = sCount::updateCount ($id, mCount::TYPE_COMMENT, $count_name, $status);
        //todo: 是否需要报错提示,不需要更新
        if (!$count)
            return false;

        $mComment = new mComment;
        $comment = $mComment->get_comment_by_id($id);
        if (!$comment)
            return error('COMMENT_NOT_EXIST');

        $action_name = strtoupper($count_name).'_ASK';

        $count_name  = $count_name.'_count';
        if(!isset($comment->$count_name)) {
            return error('WRONG_ARGUMENTS');
        }

        $value = 0;
        if ($count->status == mCount::STATUS_NORMAL){
            $value = 1;
        }
        else{
            $value = -1;
            $action_name = 'CANCEL_'.$action_name;
        }
        // 通过名字获取日志记录的键值
        $key   = sActionLog::getActionKey($action_name);
        sActionLog::init( $key, $comment );

        $comment->$count_name = max( 0, $comment->$count_name + $value ); //最小也就0

        $ret    = $comment->save();
        sActionLog::save( $comment );

        return true;
    }

    /**
     * 通过ask的id数组获取ask对象
     * @param [array] ask_ids
     * @return [array][object]
     */
    public static function umengListUserCommentCount($comment_ids) {
        if(!$comment_ids){
            return error('CODE_WRONG_INPUT');
        }
        $comment = new mComment();
        return $comment->list_user_comment_count($comment_ids);
    }

    public static function getAtComment($comment) {
        $at_comments = $comment->get_at_comments();

        $data = array();
        foreach($at_comments as $row) {
            $data[] = self::brief($row);
        }

        return $data;
    }

    public static function brief ($comment) {
        if(!$comment)
            return array();
        return array(
            'comment_id' => $comment->id,
            'uid'        => $comment->commenter->uid,
            'nickname'   => $comment->commenter->nickname,
            'avatar'     => $comment->commenter->avatar,
            'content'    => $comment->content,
        );
    }

    public static function detail ($comment) {
        if(!$comment)
            return array();
        $uid = _uid();

        return $arr = array(
            'uid'        => $comment->commenter->uid,
            'avatar'     => $comment->commenter->avatar,
            'sex'        => $comment->commenter->sex,
            'reply_to'   => $comment->reply_to,
            'for_comment'=> $comment->for_comment,
            'comment_id' => $comment->id,
            'nickname'   => $comment->commenter->nickname,
            'content'    => $comment->content,
            'up_count'      => mComment::format($comment->up_count),
            'down_count'    => mComment::format($comment->down_count),
            'inform_count'  => mComment::format($comment->inform_count),
            'create_time'   => $comment->create_time,
            'at_comment'    => self::getAtComment($comment),
            'target_id'     => $comment->target_id,
            'target_type'   => $comment->type,
            'uped'          => sCount::hasOperatedComment( $uid, $comment->id, 'up')
        );
    }

    public static function getCommentById( $id ){
        $mComment = new mComment();
        $comment = $mComment->where('id',$id)->first();
        if( !$comment ){
            return error('COMMENT_NOT_EXIST');
        }

        return $comment;
    }





    //deprecated
    public static function updateMsg( $uid, $last_fetch_time, $page = 1, $size = 15 ){
        $lasttime = sUsermeta::readUserMeta( $uid, mUsermeta::KEY_LAST_READ_COMMENT );
        $last_read_msg_time = $lasttime?$lasttime[mUsermeta::KEY_LAST_READ_COMMENT]: 0;
        define('COMMENT_MSG_TEXT', 'uid::uid: commented ur thread id::target_id: ');

        $unreadComments = new mComment();
        $unreadComments->getUnreadMsgs($uid, $last_fetch_time, $last_read_msg_time);

        foreach ($comments as $row) {
            Message::newComment(
                $row->uid,
                $uid,
                str_replace(array(':uid:',':target_id:'), array($row->uid, $row->target_id), COMMENT_MSG_TEXT),
                $row->id
            );
        }

        if(isset($row)){
            sUsermeta::refresh_read_notify(
                $uid,
                mUsermeta::KEY_LAST_READ_COMMENT,
                $row->create_time
            );
        }

        return $comments;
    }

    public static function getUnreadComments( $uid, $last_fetch_msg_time ){
        $ownAskIds = (new mAsk)->get_ask_ids_by_uid( $uid ); 
            
        $ownReplyIds = (new mReply)->where( [
                'uid' => $uid,
                'status' => mReply::STATUS_NORMAL
            ] )
            ->lists( 'id' );
            
        $ownCommentIds = (new mComment)->where( ['status'=>mComment::STATUS_NORMAL ])
            ->where(function( $query )use ( $uid ){
                $query->where('uid', $uid )
                    ->orWhere( 'reply_to', $uid );
            })
            ->lists( 'id' );
            

        $relatedComments = (new mComment)->where(function($query) use( $ownAskIds, $ownReplyIds, $ownCommentIds){
            if( !$ownAskIds->isEmpty() ){
                $query->orWhere(function($query2 ) use ( $ownAskIds){
                    $query2->where( 'type', mComment::TYPE_ASK )
                        ->whereIn( 'target_id', $ownAskIds );
                });
            }
            if( !$ownReplyIds->isEmpty() ){
                $query->orWhere( function( $query2 ) use( $ownReplyIds ){
                    $query2->where( 'type', mComment::TYPE_REPLY )
                        ->whereIn( 'target_id', $ownReplyIds );
                });
            }
            if( !$ownCommentIds->isEmpty() ){
                $query->orWhere( function( $query2 ) use ( $ownCommentIds ) {
                    $query2->whereIn( 'for_comment', $ownCommentIds );
                });
            }
        })
        ->where('update_time','>', $last_fetch_msg_time )
        ->get();

        return $relatedComments;
    }
}
