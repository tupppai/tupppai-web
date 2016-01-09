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
            $data[] = self::parse($row->type, $row->id);
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
        $tag_ids      = isset( $cond['tag_ids']      ) ? $cond['tag_ids']      : NULL;

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
                ->tags( $tag_ids )
                ->get_threads( $page, $size );

        return $result;
    }

    public static function getAllThreads( $page, $size ){
        $asks = (new mAsk)->selectRaw('asks.id, 1 as type, asks.create_time, asks.update_time')
                    ->where('status', '!=', mAsk::STATUS_DELETED)
                    ->where('status', '!=', mAsk::STATUS_BLOCKED);
        $replies = (new mReply)->selectRaw('replies.id, 2 as type, replies.create_time, replies.update_time')
                    ->where('status', '!=', mReply::STATUS_DELETED)
                    ->where('status', '!=', mReply::STATUS_BLOCKED);

        if( $page && $size ){
            return $asks->union($replies)
                        ->orderBy('create_time','DESC')
                        ->forPage( $page, $size )
                        ->get();
        }
        else{
            return $asks->count() + $replies->count();
        }
    }
}
