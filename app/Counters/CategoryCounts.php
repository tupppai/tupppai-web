<?php namespace App\Counters;

use App\Models\Ask as mAsk;
use App\Models\Count as mCount;
use App\Models\Reply as mReply;
use App\Models\Comment as mComment;
use App\Models\Download as mDownload;
use App\Models\ThreadCategory as mThreadCategory;

use App\Services\Ask as sAsk;
use App\Services\Count as sCount;
use App\Services\Reply as sReply;
use App\Services\Comment as sComment;
use App\Services\Download as sDownload;
use App\Services\ThreadCategory as sThreadCategory;

use Cache;

class CategoryCounts extends CounterBase {
    public static $key  = 'category_';

    public static function _key($category_id) {
        $key = self::$key . $category_id;

        return $key;
    }

    /**
     * 获取计数数据
     */
    public static function get($category_id) {
        $key = self::_key($category_id);

        return self::query($key, function() use ( $key, $category_id ) {
			$askAmounts = $replyAmounts = [
				'click' => 0,
				'up' => 0,
				'share' => 0,
				'comment' => 0
			];

			$askIds = sThreadCategory::getThreadIdsByCategoryId( $category_id, mCount::TYPE_ASK );
			if( !$askIds->isEmpty() ){
				$askAmounts['click'] = sAsk::sumClickByAskIds( $askIds );
				$askAmounts['up']    = sCount::countActionByTargetType( mCount::TYPE_ASK, $askIds, mCount::ACTION_UP);
				$askAmounts['share'] = sCount::countActionByTargetType( mCount::TYPE_ASK, $askIds, mCount::ACTION_SHARE);
				$askAmounts['comment'] = sComment::countByTargetIds( mComment::TYPE_ASK, $askIds );
			}

			$replyIds = sThreadCategory::getThreadIdsByCategoryId( $category_id, mCount::TYPE_REPLY );
			if( !$replyIds->isEmpty() ){
				$replyAmounts['click'] = sReply::sumClickByReplyIds( $replyIds );
				$replyAmounts['up'] = sCount::countActionByTargetType( mCount::TYPE_REPLY, $replyIds, mCount::ACTION_UP);
				$replyAmounts['share'] = sCount::countActionByTargetType( mCount::TYPE_REPLY, $replyIds, mCount::ACTION_SHARE);
				$replyAmounts['comment'] = sComment::countByTargetIds( mComment::TYPE_REPLY, $replyIds );
			}


			$download_count = (new mDownload)->where('category_id')
						->where('status', '>=', mDownload::STATUS_DELETED)
						->count();

			$counts = [
				'click_count' => $askAmounts['click'] + $replyAmounts['click'],
				'uped_count'  => $askAmounts['up'] + $replyAmounts['up'],
				'up_count'  => $askAmounts['up'] + $replyAmounts['up'],
				'replies_count' => count($replyIds),
				'download_count' => $download_count,
				'share_count' => $askAmounts['share'] + $replyAmounts['share'],
				'comment_count' => $askAmounts['comment'] + $replyAmounts['comment']
			];

            return self::put($key, $counts );
        });
    }
    public static function inc($category_id, $field, $val = 1) {
        $counts = self::get($category_id);
		$counts[$field.'_count'] += $val;
        return self::put( self::_key( $category_id ), $counts);
    }
}
