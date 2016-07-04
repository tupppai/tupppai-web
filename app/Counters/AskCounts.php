<?php namespace App\Counters;

use App\Models\Ask as mAsk;
use App\Models\Count as mCount;
use App\Models\Focus as mFocus;
use App\Models\Reply as mReply;
use App\Models\Inform as mInform;
use App\Models\Comment as mComment;
use App\Models\Download as mDownload;

use App\Services\Ask as sAsk;
use App\Services\Count as sCount;
use App\Services\Focus as sFocus;
use App\Services\Reply as sReply;
use App\Services\Reward as sReward;
use App\Services\Inform as sInform;
use App\Services\Comment as sComment;
use App\Services\Download as sDownload;

use Cache;

class AskCounts extends CounterBase {
    public static $key  = 'ask_';

    public static function _key($ask_id) {
        $key = self::$key . $ask_id;

        return $key;
    }

    /**
     * 获取计数数据
     */
    public static function get($ask_id) {
        $key = self::_key($ask_id);

        return self::query($key, function() use ( $key, $ask_id ) {
			$ask = (new mAsk)->find($ask_id);
			$click_count = $ask->click_count;

            $download_count = sDownload::countAskDownloads( $ask_id );

			$focus_count = sFocus::countFocusesByAskId( $ask_id );

			$inform_count = sInform::countTargetReportTimes( mInform::TYPE_REPLY,$ask_id);

			$reply_count = sReply::getRepliesCountByAskId($ask_id);

			$comment_count = sComment::countByTargetId( mComment::TYPE_ASK, $ask_id);

			$reward_count = sReward::getUserRewardAskCount($ask_id);

            $counts = [
				'up_count'
					=> sCount::countActionByTarget( mCount::TYPE_ASK, $ask_id, mCount::ACTION_UP ),
				'like_count'
					=> sCount::countActionByTarget( mCount::TYPE_ASK, $ask_id, mCount::ACTION_LIKE ),
				'share_count'
					=> sCount::countActionByTarget( mCount::TYPE_ASK, $ask_id, mCount::ACTION_SHARE ),
				'weixin_share_count'
					=> sCount::countActionByTarget( mCount::TYPE_ASK, $ask_id, mCount::ACTION_WEIXIN_SHARE ),
				'timeline_share_count'
					=> sCount::countActionByTarget( mCount::TYPE_ASK, $ask_id, mCount::ACTION_TIMELINE_SHARE ),

				'click_count'    => $click_count,

				'comment_count'  => $comment_count,
				'reply_count'    => $reply_count,
				'inform_count'   => $inform_count,
				'focus_count'    => $focus_count,
				'collect_count'  => $focus_count, //相同与focus_count
				'download_count' => $download_count,
				'reward_count'   => $reward_count
            ];

            return self::put($key, $counts );
        });
    }

    public static function inc($ask_id, $field, $val = 1) {
        $counts = self::get($ask_id);
		$counts[$field.'_count'] += $val;

		if( $field == 'click' ){
			//每点击50次，存进数据库
			if($counts[$field.'_count'] % 50 == 0) {
	            $ask = mAsk::find($ask_id);
	            $ask->click_count = $counts[$field.'_count'];
	            $ask->save();
	        }
		}

        return self::put( self::_key( $ask_id ), $counts);
    }
}
