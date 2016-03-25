<?php namespace App\Counters;

use App\Models\Ask as mAsk;
use App\Models\Count as mCount;
use App\Models\Reply as mReply;
use App\Models\Follow as mFollow;
use App\Models\Download as mDownload;
use App\Models\Collection as mCollection;

use App\Services\Ask as sAsk;
use App\Services\Count as sCount;
use App\Services\Reply as sReply;
use App\Services\Follow as sFollow;
use App\Services\Download as sDownload;
use App\Services\Collection as sCollection;
use App\Services\Inform as sInform;
use App\Services\Comment as sComment;

use Cache;

class UserCounts extends CounterBase {
    public static $key  = 'user_';

    public static function _key($user_id) {
        $key = self::$key . $user_id;

        return $key;
    }

    /**
     * 获取计数数据
     */
    public static function get($user_id) {
        $key = self::_key($user_id);

        return self::query($key, function() use ( $key, $user_id ) {
            $ask_count  = sAsk::getUserAskCount( $user_id );

			$collect_count = sCollection::countCollectionsByReplyId( $user_id );

			$download_count = (new mDownload)->distinct()
		                    ->where('uid', $user_id)
		                    ->where('status', '>', mDownload::STATUS_DELETED)
		                    ->count();

		    $inprogress_count = sDownload::countUserDownload( $user_id );
            $fans_amount   = sFollow::countUserFans( $user_id );

		    $follow_amount = sFollow::countUserFollow( $user_id );

			$reply_count   = sReply::countUserReply( $user_id );
            $comment_count = sComment::countByUid( $user_id );
            $uped_count    = self::upedAmounts( $user_id ); //被点了多少赞

			$reply_count = sReply::countUserReply( $user_id );
			$comment_count = sComment::countByUid( $user_id );
			$up_count = sCount::countActionByUid( $user_id, mCount::ACTION_UP );
			$counts = [
				'ask_count'      => $ask_count,
				'badges_count'   => 0,
				'collect_count'  => $collect_count,// add focus count?
				'download_count' => $download_count,
				'fans_count'     => $fans_amount,
				'fellow_count'   => $follow_amount,
				'inform_count'   => sInform::countReportedTimesByUid( $user_id ),
				'report_count'   => sInform::countReportTimes( $user_id ),
				'reply_count'    => $reply_count,
				'up_count'       => $up_count, //点了多少赞
				'uped_count'     => self::upedAmounts( $user_id ), //被点了多少赞(获赞)
				'inprogress_count' => $inprogress_count,
				'comment_count' => $comment_count,
			];

            return self::put($key, $counts );
        });
    }

    public static function inc($user_id, $field, $val = 1) {
        $counts = self::get($user_id);
		$counts[$field.'_count'] += $val;
        return self::put( self::_key( $user_id ), $counts);
    }

    public static function reset($user_id, $field) {
        $counts = self::get($user_id);
		$counts[$field.'_count'] = 0;

        self::put(self::_key($user_id), $counts );
    }


    protected static function upedAmounts( $user_id ){
		$askUpedAmount = self::askUpedAmounts( $user_id );
		$replyUpedAmount = self::replyUpedAmounts( $user_id );
		return $askUpedAmount + $replyUpedAmount;
    }

    protected static function askUpedAmounts( $user_id ){
		$askIds = (new mAsk)->where('uid', $user_id )
							->select( 'id' )
							->get();

		$amounts = (new mCount)->whereIn('target_id', $askIds )
						->where('type', mCount::TYPE_ASK )
						->where('status', '>', mCount::STATUS_NORMAL )
						->count();
		return $amounts;
    }

    protected static function replyUpedAmounts( $user_id ){
		$replyIds = (new mReply)->where('uid', $user_id )
							->select( 'id' )
							->get();

		$amounts = (new mCount)->whereIn('target_id', $replyIds )
						->where('type', mCount::TYPE_REPLY )
						->where('status', '>', mCount::STATUS_NORMAL )
						->count();
		return $amounts;
    }
}
