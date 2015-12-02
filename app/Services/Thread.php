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
        $cond = [
            'category_ids' => null,
            'target_type'  => array('ask', 'reply'),
            'thread_type'  => null,
            'user_type'    => null,
            'user_role'    => null,
            'uid'          => null,
            'thread_id'    => null,
            'desc'         => $desc,
            'nickname'     => null,
            'type'         => null
            ];
        $ids = self::getThreadIds($cond, $page, $size);

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
        $category_ids = isset( $cond['category_ids'] ) ? $cond['category_ids'] : NULL;
        $target_type  = isset( $cond['target_type']  ) ? $cond['target_type']  : ['ask','reply'];
        $thread_type  = isset( $cond['thread_type']  ) ? $cond['thread_type']  : NULL;
        $user_type    = isset( $cond['user_type']    ) ? $cond['user_type']    : NULL;
        $user_role    = isset( $cond['user_role']    ) ? $cond['user_role']    : NULL;
        $uid          = isset( $cond['uid']          ) ? $cond['uid']          : NULL;
        $thread_id    = isset( $cond['thread_id']    ) ? $cond['thread_id']    : NULL;
        $desc         = isset( $cond['desc']         ) ? $cond['desc']         : NULL;
        $nickname     = isset( $cond['nickname']     ) ? $cond['nickname']     : NULL;
        $type         = isset( $cond['type']         ) ? $cond['type']         : NULL;

        $mUser = new mUser();
        $mThread = new mThread();
        $result = $mThread->threadType( $thread_type )
                ->targetType( $target_type )
                ->categories( $category_ids )
                ->userType( $user_type )
                ->userRole( $user_role )
                ->uid( $uid )
                ->type( $type )
                ->nickname( $nickname )
                ->threadId( $thread_id )
                ->desc( $desc )
                ->get_threads( $page, $size );

        return $result;
    }

    public static function parseAskAndReply( $ts ){
        //bug 会出现删除的？
        $threads = array();
        foreach( $ts as $key=>$value ){
            switch( $value->type ){
            case mReply::TYPE_REPLY:
                $reply = sReply::getReplyById($value->target_id) ;
                if(!$reply) continue;
                $reply = sReply::detail( $reply );
                array_push( $threads, $reply );
                break;
            case mAsk::TYPE_ASK:
                $ask = sAsk::getAskById( $value->target_id );
                if(!$ask) continue;
                $ask = sAsk::detail( $ask );
                array_push( $threads, $ask );
                break;
            }
        }

        return $threads;
    }
}
