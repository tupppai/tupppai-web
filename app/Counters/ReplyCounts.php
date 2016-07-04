<?php namespace App\Counters;

use App\Models\Count as mCount;
use App\Models\Reply as mReply;
use App\Models\Inform as mInform;
use App\Models\Comment as mComment;
use App\Models\Collection as mCollection;

use App\Services\Count as sCount;
use App\Services\Reply as sReply;
use App\Services\Reward as sReward;
use App\Services\Inform as sInform;
use App\Services\Comment as sComment;
use App\Services\Collection as sCollection;

use Cache;

class ReplyCounts extends CounterBase {
    public static $key  = 'reply_';

    public static function _key($reply_id) {
        $key = self::$key . $reply_id;

        return $key;
    }

    /**
     * 获取计数数据
     */
    public static function get($reply_id) {
        $key = self::_key($reply_id);

        return self::query($key, function() use ( $key, $reply_id ) {
			$reply = (new mReply)->find($reply_id);
			$click_count = $reply->click_count;

			$inform_count = sInform::countTargetReportTimes( mInform::TYPE_REPLY,$reply_id);

			$comment_count = sComment::countByTargetId( mComment::TYPE_REPLY, $reply_id);
			$collect_count = sCollection::countCollectionsByReplyId($reply_id);
            $uped_count = sCount::sumLoveByTarget( mCount::TYPE_REPLY, $reply_id);
            $reward_count = sReward::countRewardReplyUserAmount( $reply_id );


            $counts = [
				'up_count' => $uped_count,
				'uped_count' => $uped_count,
                'like_count'
					=> sCount::countActionByTarget( mCount::TYPE_REPLY, $reply_id, mCount::ACTION_LIKE ),
				'share_count'
					=> sCount::countActionByTarget( mCount::TYPE_REPLY, $reply_id, mCount::ACTION_SHARE ),
				'weixin_share_count'
					=> sCount::countActionByTarget( mCount::TYPE_REPLY, $reply_id, mCount::ACTION_WEIXIN_SHARE ),
				'timeline_share_count'
					=> sCount::countActionByTarget( mCount::TYPE_REPLY, $reply_id, mCount::ACTION_TIMELINE_SHARE ),

				'click_count'    => $click_count,

				'comment_count'  => $comment_count,
				'inform_count'   => $inform_count,
				'collect_count'  => $collect_count,
				'reward_count'	 => $reward_count
            ];

            return self::put($key, $counts );
        });
    }

    public static function inc($reply_id, $field, $val = 1) {
        $counts = self::get($reply_id);
		$counts[$field.'_count'] += $val;

		if( $field == 'click' ){
			//每点击50次，存进数据库
			if($counts[$field.'_count'] % 50 == 0) {
	            $reply = mReply::find($reply_id);
	            $reply->click_count = $counts[$field.'_count'];
	            $reply->save();
	        }
		}

        return self::put( self::_key( $reply_id ), $counts);
    }
}
