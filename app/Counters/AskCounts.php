<?php namespace App\Counters;

use App\Models\Ask as mAsk;
use App\Models\Count as mCount;
use App\Models\Focus as mFocus;
use App\Models\Reply as mReply;
use App\Models\Inform as mInform;
use App\Models\Comment as mComment;
use App\Models\Download as mDownload;

use App\Services\Count as sCount;

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

            $download_count = (new mDownload)->where('type', mDownload::TYPE_ASK)
										->where('target_id', $ask_id)
										->where('status', mDownload::STATUS_NORMAL)
										->count();

			$focus_count = (new mFocus)->where('ask_id', $ask_id)
					                ->where('status', mFocus::STATUS_NORMAL)
					                ->count();

			$inform_count = (new mInform)->where('target_type', mInform::TYPE_ASK)
										->where('target_id', $ask_id)
										->where('status', mInform::STATUS_NORMAL)
										->count();

			$reply_count = (new mReply)->where('ask_id', $ask_id)
										->where('status', '>', mReply::STATUS_DELETED)
										->count();

			$comment_count = (new mComment)->where('type', mComment::TYPE_ASK)
										->where('target_id', $ask_id)
										->where('status', mComment::STATUS_NORMAL)
										->count();
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
				'download_count' => $download_count
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
	            $ask->click_count = $count;
	            $ask->save();
	        }
		}

        return self::put( self::_key( $ask_id ), $counts);
    }
}
