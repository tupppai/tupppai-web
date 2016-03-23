<?php namespace App\Counters;

use App\Models\Ask as mAsk;
use App\Models\Count as mCount;
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
            $counts = [
				'up_count'
					=> sCount::countActionByTarget( mCount::TYPE_ASK, $ask_id, mCount::ACTION_UP ),
				'like_count'
					=> sCount::countActionByTarget( mCount::TYPE_ASK, $ask_id, mCount::ACTION_LIKE ),
				'collect_count'
					=> sCount::countActionByTarget( mCount::TYPE_ASK, $ask_id, mCount::ACTION_COLLECT ),
				'share_count'
					=> sCount::countActionByTarget( mCount::TYPE_ASK, $ask_id, mCount::ACTION_SHARE ),
				'weixin_share_count'
					=> sCount::countActionByTarget( mCount::TYPE_ASK, $ask_id, mCount::ACTION_WEIXIN_SHARE ),
				'inform_count'
					=> sCount::countActionByTarget( mCount::TYPE_ASK, $ask_id, mCount::ACTION_INFORM ),
				'comment_count'
					=> sCount::countActionByTarget( mCount::TYPE_ASK, $ask_id, mCount::ACTION_COMMENT ),
				'reply_count'
					=> sCount::countActionByTarget( mCount::TYPE_ASK, $ask_id, mCount::ACTION_REPLY ),
				'timeline_share_count'
					=> sCount::countActionByTarget( mCount::TYPE_ASK, $ask_id, mCount::ACTION_TIMELINE_SHARE ),


				'click_count' => $click_count
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
