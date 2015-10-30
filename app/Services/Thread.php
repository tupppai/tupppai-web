<?php
namespace App\Services;
use DB;
use Illuminate\Pagination\Paginator;
use App\Services\Ask as sAsk,
    App\Services\Reply as sReply,
    App\Services\ThreadCategory as sThreadCategory;

use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;

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

        // 优化一下
        foreach($threads as $thread) {
            if($thread->target_type == mThreadCategory::TYPE_ASK) {
                $ask_ids[] = $thread->target_id;
            }
            else if($thread->target_type == mThreadCategory::TYPE_REPLY) {
                $reply_ids[] = $thread->target_id;
            }
        }
        $asks   = sAsk::getAsksByIds($ask_ids);
        $replies= sReply::getRepliesByIds($reply_ids);

        $sort_arr = array();
        foreach($asks as $ask) {
            $sort_arr[$ask->create_time] = sAsk::detail($ask);
        }
        foreach($replies as $reply) {
            $sort_arr[$reply->create_time] = sReply::detail($reply);
        }
        sort($sort_arr);

        return array_values($sort_arr);
    }

    public static function getThreadIds($cond, $page, $size){
        $mAsk   = new mAsk;
        $mReply = new mReply;
        $tcTable = (new mThreadCategory())->getTable();

        $asks   = DB::table('asks')->selectRaw('asks.id, 1 as type, asks.create_time, asks.update_time')
                    ->leftJoin( $tcTable, function( $join ) use ( $tcTable, $cond ) {
                        $join->on( 'asks.id', '=', $tcTable.'.target_id' )
                             ->where( 'target_type', '=', 1 )
                             ->where( $tcTable.'.status', '!=', mAsk::STATUS_DELETED);
                    });
            //->where( 'category_id', $category_id );
        $replies= DB::table('replies')->selectRaw('replies.id, 2 as type, replies.create_time, replies.update_time')
                    ->leftJoin( $tcTable, function( $join ) use ( $tcTable, $cond ) {
                        $join->on( 'replies.id', '=', $tcTable.'.target_id' )
                            ->where( 'target_type', '=', 2 )
                            ->where( $tcTable.'.status', '!=', mAsk::STATUS_DELETED);
                    });
            //->where( 'category_id', $category_id );
        if( isset( $cond['status'] ) ){
            if( is_array( $cond['status'] ) ){
                $asks = $asks->whereIn( $tcTable.'.status', $cond['status'] );
                $replies = $replies->whereIn( $tcTable.'.status', $cond['status'] );
            }
            else{
                $asks = $asks->where( $tcTable.'.status', '=', $cond['status'] );
                $replies = $replies->where( $tcTable.'.status', '=', $cond['status'] );
            }

        }

        if( isset( $cond['category_id'] ) ){
            $asks->where( 'category_id', '=', $cond['category_id'] );
            $replies->where( 'category_id', '=', $cond['category_id'] );
        }


        //todo sky 先count再forpage
        $askAndReply = $asks->union($replies)
            ->orderBy('create_time','DESC');
            //->forPage( $page, $size );


        $askAndReply = $askAndReply->get();

        $total = count( $askAndReply );

        $offset = ($page-1)*$size;
        $result = array_slice( $askAndReply, $offset, $size );

        return ['result' => $result, 'total' => $total];
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
