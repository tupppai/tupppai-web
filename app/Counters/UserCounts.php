<?php namespace App\Counters;

use App\Models\Ask as mAsk;
use App\Models\Count as mCount;
use App\Models\Reply as mReply;
use App\Models\Follow as mFollow;
use App\Models\Download as mDownload;
use App\Models\Collection as mCollection;

use App\Services\Count as sCount;
use App\Services\Inform as sInform;

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
            $ask_count  = (new mAsk)->where('uid', $user_id)
				                ->valid()
				                ->count();

			$collect_count = (new mCollection)->where('uid', $user_id)
					                ->valid()
					                ->count();

			$download_count = (new mDownload)->distinct()
		                    ->where('uid', $user_id)
		                    ->where('status', '>', mDownload::STATUS_DELETED)
		                    ->count();

		    $inprogress_count = (new mDownload)->distinct()
		                    ->where('uid', $user_id)
		                    ->where('status', '=', mDownload::STATUS_NORMAL)
		                    ->count();

            $fans_amount  = (new mFollow)->where('follow_who', $user_id)
			                ->where('uid', '!=', $user_id)
		                    ->where('status', '>', mDownload::STATUS_DELETED)
			                ->count();

		    $follow_amount = (new mFollow)->where('uid', $user_id)
			                ->where('follow_who', '!=', $user_id)
							->where('status', '>', mDownload::STATUS_DELETED)
			                ->count();

			$reply_count = (new mReply)->where('uid', $user_id )
	                        ->where('status', '>', mDownload::STATUS_DELETED)
							->count();

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
				'up_count'       => self::upedAmounts( $user_id ), //被点了多少赞
				'inprogress_count' => $inprogress_count,
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
