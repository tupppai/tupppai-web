<?php namespace App\Counters;

use App\Models\Count as mCount;
use App\Models\Reply as mReply;
use App\Models\Inform as mInform;
use App\Models\Comment as mComment;
use App\Models\Collection as mCollection;

use App\Services\Count as sCount;

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

			$inform_count = (new mInform)->where('target_type', mInform::TYPE_REPLY)
										->where('target_id', $reply_id)
										->where('status', mInform::STATUS_NORMAL)
										->count();

			$comment_count = (new mComment)->where('type', mComment::TYPE_REPLY)
										->where('target_id', $reply_id)
										->where('status', mComment::STATUS_NORMAL)
										->count();
			$collect_count = (new mCollection)->where('reply_id', $reply_id)
											->where('status', mComment::STATUS_NORMAL)
											->count();

            $counts = [
				'up_count'
					=> sCount::countActionByTarget( mCount::TYPE_REPLY, $reply_id, mCount::ACTION_UP ),
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
	            $reply->click_count = $count;
	            $reply->save();
	        }
		}

        return self::put( self::_key( $reply_id ), $counts);
    }
}