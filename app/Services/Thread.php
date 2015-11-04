<?php
namespace App\Services;
use DB;
use Illuminate\Pagination\Paginator;
use App\Services\Ask as sAsk,
    App\Services\Reply as sReply,
    App\Services\ThreadCategory as sThreadCategory;

use App\Models\Ask as mAsk;
use App\Models\User as mUser;
use App\Models\Reply as mReply;
use App\Models\Thread as mThread;

use App\Models\ThreadCategory as mThreadCategory;

class Thread extends ServiceBase
{
    public static function getPopularThreads($uid, $page, $size, $last_updated, $type){
        $threads    = sThreadCategory::getPopularThreads( $type, $page, $size );
        $ask_ids    = array();
        $reply_ids  = array();

        $data = array();
        foreach($threads as $thread) {
            if($thread->target_type == mThreadCategory::TYPE_ASK) {
                $data[] = sAsk::detail(sAsk::getAskById($thread->target_id));
            }
            else if($thread->target_type == mThreadCategory::TYPE_REPLY) {
                $data[] = sReply::detail(sReply::getReplyById($thread->target_id));
            }
        }

        return $data;
    }

    public static function searchThreads($desc, $page, $size) {
        $ids = self::getThreadIds(array(
            'desc'=>$desc
        ), $page, $size);

        $data = array();
        foreach($ids['result'] as $row) {
            if($row->type == mThreadCategory::TYPE_ASK) {
                $data[] = sAsk::detail(sAsk::getAskById($row->id));
            }
            else if($row->type == mThreadCategory::TYPE_REPLY) {
                $data[] = sReply::detail(sReply::getReplyById($row->id));
            }
        }
        return $data;
    }

    public static function getThreadIds( $cond, $page, $size ){
        $target_type = $cond['target_type'] ;
        $thread_type = $cond['thread_type'];
        $user_type   = $cond['user_type'];
        $user_role   = $cond['user_role'];
        $uid         = $cond['uid'];
        $thread_id   = $cond['thread_id'];
        $desc        = $cond['desc'];
        $nickname    = $cond['nickname'];

        $mUser = new mUser();
        $mThread = new mThread();

        if( !$uid  && $nickname ){
            $user = $mUser->get_user_by_nickname( $nickname );
            $uid = $user['uid'];
        }

        $result = $mThread->threadType( $thread_type )
                ->targetType( $target_type )
                ->userType( $user_type )
                ->userRole( $user_role )
                ->uid( $uid )
                ->threadId( $thread_id )
                ->desc( $desc )
                ->get_threads( $page, $size );

        return $result;
    }

    public static function parseAskAndReply( $ts ){
        $threads = array();
        foreach( $ts as $key=>$value ){
            switch( $value->type ){
                case mReply::TYPE_REPLY:
                    $reply = sReply::detail( sReply::getReplyById($value->target_id) );
                    array_push( $threads, $reply );
                    break;
                case mAsk::TYPE_ASK:
                    $ask = sAsk::detail( sAsk::getAskById( $value->target_id, false) );
                    array_push( $threads, $ask );
                    break;
            }
        }

        return $threads;
    }
}
