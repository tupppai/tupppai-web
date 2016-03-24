<?php namespace App\Counters;

use App\Models\Ask as mAsk;
use App\Models\Count as mCount;
use App\Models\Reply as mReply;
use App\Models\Comment as mComment;
use App\Models\Download as mDownload;
use App\Models\ThreadCategory as mThreadCategory;
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

			$askIds = (new mThreadCategory)->where('target_type', mThreadCategory::TYPE_ASK )
							->where( 'category_id', $category_id )
							->where( 'status', '>', mThreadCategory::STATUS_DELETED )
							->select( 'target_id' )
							->get();
			if( !$askIds->isEmpty() ){
				$askAmounts['click'] = (new mAsk)->whereIn('id', $askIds)
										->where('status', '>=', mAsk::STATUS_DELETED)
										->sum('click_count');
				$askAmounts['up']    = (new mCount)->whereIn('target_id', $askIds )
										->where('type', mCount::TYPE_ASK)
										->where('status', '>=', mCount::STATUS_DELETED)
										->where('action', mCount::ACTION_UP)
										->count();
				$askAmounts['share'] = (new mCount)->whereIn( 'target_id', $askIds )
											->where('type', mCount::TYPE_ASK)
											->where('status', '>=', mCount::STATUS_DELETED)
											->where('action', mCount::ACTION_SHARE)
											->count();
				$askAmounts['comment'] = (new mComment)->whereIn( 'target_id', $askIds )
											->where('type', mComment::TYPE_ASK)
											->where('status', '>=', mCount::STATUS_DELETED)
											->count();
			}

			$replyIds = (new mThreadCategory)->where('target_type', mThreadCategory::TYPE_REPLY )
							->where( 'category_id', $category_id )
							->where( 'status', '>', mThreadCategory::STATUS_DELETED )
							->select( 'target_id' )
							->get();
			if( !$replyIds->isEmpty() ){
				$replyAmounts['click'] = (new mReply)->whereIn('id', $replyIds)
									->where('status', '>=', mAsk::STATUS_DELETED)
									->sum('click_count');
				$replyAmounts['up'] = (new mCount)->whereIn('target_id', $replyIds )
										->where('type', mCount::TYPE_REPLY)
										->where('status', '>=', mCount::STATUS_DELETED)
										->where('action', mCount::ACTION_UP)
										->count();
				$replyAmounts['share'] = (new mCount)->whereIn( 'target_id', $replyIds )
											->where('type', mCount::TYPE_REPLY)
											->where('status', '>=', mCount::STATUS_DELETED)
											->where('action', mCount::ACTION_SHARE)
											->count();
				$replyAmounts['comment'] = (new mComment)->whereIn( 'target_id', $replyIds )
											->where('type', mComment::TYPE_REPLY )
											->where('status', '>=', mCount::STATUS_DELETED)
											->count();
			}


			$download_count = (new mDownload)->where('category_id')
						->where('status', '>=', mDownload::STATUS_DELETED)
						->count();

			$click_count = self::sumClickCount( $category_id );
			$counts = [
				'click_count' => $askAmounts['click'] + $replyAmounts['click'],
				'uped_count'  => $askAmounts['up'] + $replyAmounts['up'],
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

    private static function sumClickCount( $category_id ){

    }
}
