<?php namespace App\Services;

use DB;

use App\Models\Comment as mComment,
    App\Models\Count  as mCount,
    App\Models\Ask as mAsk,
    App\Models\Reply as mReply,
    App\Models\Usermeta as mUsermeta;

use App\Services\Count as sCount,
    App\Services\Ask as sAsk,
    App\Services\Usermeta as sUsermeta,
    App\Services\User as sUser,
    App\Services\Reply as sReply,
    App\Services\Reward as sReward,
    App\Services\SysMsg as sSysMsg,
    App\Services\Message as sMessage,
    App\Services\ThreadCategory as sThreadCategory,
    App\Services\ActionLog as sActionLog;

use App\Counters\UserCounts as cUserCounts;
use App\Counters\ReplyCounts as cReplyCounts;
use Queue, App\Jobs\Push;
use Redis;

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
        $mComment   = new mComment;
        $msg_type   = 'comment';

        switch( $type ){
            case mComment::TYPE_ASK:
                $target     = $mAsk->get_ask_by_id($target_id);
                if(!$target) {
                    return error('ASK_NOT_EXIST');
                }
                $reply_to   = $target->uid;
                $msg_type   = 'comment_ask';
                break;
            case mComment::TYPE_REPLY:
                $target     = $mReply->get_reply_by_id($target_id);
                if(!$target) {
                    return error('REPLY_NOT_EXIST');
                }
                $reply_to   = $target->uid;
                $msg_type   = 'comment_reply';
                break;
            #case mComment::TYPE_COMMENT:
            #    $target     = $mComment->get_comment_by_id($for_comment);
            #    $reply_to   = $target->uid;
            #    $msg_type   = 'comment_comment';
            #    break;
            default:
                $reply_to = 0;
        }

        if($for_comment != 0) {
            $target     = $mComment->get_comment_by_id($for_comment);
            $reply_to   = $target->uid;
            $msg_type   = 'comment_comment';
            //评论对象红点
            cUserCounts::inc($target->uid, 'badges');
        }

        if ( !$target ) {
            return error('COMMENT_ERR');
        }
        $comment = new mComment();
        sActionLog::init('POST_COMMENT', $comment);

        $comment->assign(array(
            'uid'         => $uid,
            'content'     => emoji_to_shortname($content),
            'type'        => $type,
            'target_id'   => $target_id,
            'reply_to'    => $reply_to,
            'for_comment' => $for_comment
        ));

        $comment->save();

        if($uid != $reply_to) {
            #评论推送
            Queue::push(new Push(array(
                'uid'=>$uid,
                'target_uid'=>$reply_to,
                'type'=>$msg_type,
                'comment_id'=>$comment->id,
                'for_comment'=> !$for_comment?$for_comment:0
            )));
        }
        sActionLog::save($comment);

        switch( $type ){
            case mComment::TYPE_REPLY:
                sReply::commentReply($target_id, mCount::STATUS_NORMAL, $uid);
                $is_grad = sThreadCategory::checkedThreadAsCategoryType( mComment::TYPE_REPLY, $target_id, mAsk::CATEGORY_TYPE_GRADUATION);
                $counts = cReplyCounts::get($target_id);

                if( $is_grad && ($counts['up_count'] >30 || $counts['comment_count'] >20)){
                    //毕业季活动，增加帖子的权重
                    //Redis::zadd('grad_replies',$counts['up_count']*0.3+$counts['comment_count']*0.7, $target_id);
                }
                else{
                    //Redis::zrem('grad_replies', $target_id);
                }
                break;
            case mComment::TYPE_ASK:
                sAsk::commentAsk($target_id, mCount::STATUS_NORMAL, $uid );
                break;
            default:
                break;
        }
        $nowUser   = sUser::getUserByUid($comment->uid);
        $replyUser = sUser::getUserByUid($comment->reply_to);
        $comment->user_name  = $nowUser->nickname;
        $comment->reply_name = $replyUser->nickname;
        return $comment;
    }

    /**
     * 通过commentid获取commen
     */
    public static function getCommentById( $id ){
        $mComment = new mComment();
        $comment = $mComment->find($id);

        return $comment;
    }

    public static function getHotComments($type, $target_id) {
        $FIRST_PAGE_HOT_COMMENT_SIZE = 2; //todo save as configuration file
        $mComment = new mComment;
        $hotComments = $mComment->getHotComments( $type, $target_id, 0, $FIRST_PAGE_HOT_COMMENT_SIZE );

        $comment_arr = array();
        foreach ($hotComments as $comment) {
            $comment_arr[] = self::detail($comment);
        }

        return $comment_arr;
    }

    /**
     * 获取评论列表
     * todo: redis sort
     */
    public static function getComments($type, $target_id, $page=1, $size=10) {
        $FIRST_PAGE_HOT_COMMENT_SIZE = 60; //todo save as configuration file
        $mComment = new mComment;
        // comment 评论
        $data = array();

        $data['hot_comments'] = array();

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
     * 获取评论列表 v
     * todo: redis sort
     */
    public static function getCommentsV2($type, $target_id, $page=1, $size=10) {
        // comment 评论
        $newComments = new mComment();
        $newComments = $newComments->getNewComments( $type, $target_id, $page, $size );

        $comment_arr = array();
        foreach ($newComments as $comment) {
            $comment_arr[] = self::detail($comment, 1);//此处将model组装成前端需要的数据
        }
        $data        = $comment_arr;

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
     * 获取其回复的评论
     * @param  mComment $comment comment的模型
     * @param  integer  $limit   最多抓出多少条，若为0则不限制
     * @return array          评论brief后得到的数组
     */
    public static function getAtComment($comment, $limit) {
        $at_comments = $comment->get_at_comments();

        $data = array();
        foreach($at_comments as $row) {
            $data[] = self::brief($row);
            if($limit && count($data) >= $limit){
                break;//若有限制最大抓取条数，且当前已达到最大条数，则不继续抓取
            }
        }

        return $data;
    }

    public static function getCommentsByUid( $uid, $page, $size ){
        $mComment = new mComment();

        $comments = $mComment->get_commments_by_uid( $uid, $page, $size );
        $cArr = [];
        foreach( $comments as $comment ){
            $cArr[] = self::commentDetail( $comment );
        }
        return $cArr;
    }

    public static function commentDetail( $cmnt ){
        //$sender = sUser::brief( sUser::getUserByUid( $cmnt->uid ));
        $comment   = self::brief( $cmnt );
        if( $cmnt->type == mComment::TYPE_ASK ) {
            $ask_id = $cmnt->target_id;
            $thread = sAsk::detail( sAsk::getAskById( $ask_id ) );
        }
        else if( $cmnt->type == mComment::TYPE_REPLY ) {
            $thread = sReply::detail( sReply::getReplyById( $cmnt->target_id ) );
        }
        else{
            $thread = self::brief( self::getCommentsByUid( $cmnt->for_comment ) );
        }

        $temp['content'] = $cmnt->content;
        $temp['comment_id'] = $cmnt->id;
        $temp['comment_time'] = $cmnt->create_time;
        $temp = array_merge( $temp, $thread );

        return $temp;
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


        if($ownReplyIds->isEmpty() && $ownAskIds->isEmpty() && $ownCommentIds->isEmpty()) {
            return array();
        }

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
        ->where('create_time','>', $last_fetch_msg_time )
        ->get();

        return $relatedComments;
    }

    public static function blockUserComments( $uid ){
        sActionLog::init('BLOCK_USER_COMMENTS');
        $mComment = new mComment();
        $mComment->change_user_comments_status( $uid, mComment::STATUS_BLOCKED, mComment::STATUS_NORMAL);
        sActionLog::save();
        return true;
    }

    public static function recoverBlockedComments( $uid ){
        sActionLog::init('RESTORE_USER_COMMENTS');
        $mComment = new mComment();
        $mComment->change_user_comments_status( $uid, mComment::STATUS_NORMAL, mComment::STATUS_BLOCKED);
        sActionLog::save();
        return true;
    }

    private static function changeCommentStatus( $id, $status, $log_name ){
        //todo increase ask/reply comment_count
        sActionLog::init($log_name);
        $mComment = new mComment();
        $mComment->change_comment_status( $id, $status );
        sActionLog::save();
        return true;
    }

    public static function deleteComment( $id ){
        $ret =  self::changeCommentStatus( $id, mComment::STATUS_DELETED, 'DELETE_COMMENT' );
        $mComment = new mComment();
        $comment = $mComment->get_comment_by_id( $id );

        sSysMsg::postMsg( _uid(), '您的评论"'.$comment->content.'"已被管理员删除。', $comment->type, $comment->target_id, '', time(), $comment->uid, 'comment_delete', '' );

        Queue::push(new Push([
            'type'=>'comment_delete',
            'comment_id'=>$comment->id,
            'uid' => $comment->uid
        ]));
        return true;
        //todo:: increment/decrement ask/reply comment_count
    }

    public static function restoreComment( $id ){
        return self::changeCommentStatus( $id, mComment::STATUS_NORMAL, 'RESTORE_COMMENT' );
        //todo:: increment/decrement ask/reply comment_count
    }
    public static function blockComment( $id ){
        return self::changeCommentStatus( $id, mComment::STATUS_BLOCKED, 'RESTORE_COMMENT' );
        //todo:: increment/decrement ask/reply comment_count
    }

    public static function brief ($comment) {
        if(!$comment)
            return array();
        return array(
            'comment_id' => $comment->id,
            'uid'        => $comment->commenter->uid,
            'nickname'   => shortname_to_unicode($comment->commenter->nickname),
            'avatar'     => $comment->commenter->avatar,
            'content'    => shortname_to_unicode($comment->content)
        );
    }

    public static function detail ($comment, $atCommentLimit = 0) {
        if(!$comment)
            return array();
        $uid = _uid();

        return $arr = array(
            'uid'           => $comment->commenter->uid,
            'avatar'        => $comment->commenter->avatar,
            'sex'           => $comment->commenter->sex?1:0,
            'reply_to'      => $comment->reply_to,
            'for_comment'   => $comment->for_comment,
            'comment_id'    => $comment->id,
            'nickname'      => shortname_to_unicode($comment->commenter->nickname),
            'content'       => shortname_to_unicode($comment->content),
            'up_count'      => mComment::format($comment->up_count),
            'down_count'    => mComment::format($comment->down_count),
            'inform_count'  => mComment::format($comment->inform_count),
            'create_time'   => $comment->create_time,
            'at_comment'    => self::getAtComment($comment, $atCommentLimit),
            'target_id'     => $comment->target_id,
            'target_type'   => $comment->type,
            'uped'          => sCount::hasOperatedComment( $uid, $comment->id, 'up'),
            'has_rewarded'  => sReward::checkUserHasRewardTarget( $uid, $comment->type, $comment->target_id )
        );
    }
    public static function countByTargetId( $target_type, $target_id ){
        $mComment = new mComment();
        return $mComment->count_by_cond([
            'type'      => $target_type,
            'target_id' => $target_id
        ]);
    }

    public static function countByTargetIds( $target_type, $target_ids ){
        $mComment = new mComment();
        return $mComment->count_by_cond([
            'type'      => $target_type,
            'target_ids' => $target_ids
        ]);
    }

    public static function countByUid( $uid ){
        $mComment = new mComment();
        return $mComment->count_by_cond([
            'uid'      => $uid,
        ]);
    }
}
