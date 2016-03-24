<?php namespace App\Counters;

use App\Models\Ask as mAsk;
use App\Models\Count as mCount;
use App\Models\Reply as mReply;
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
			$askClickAmount = 0;
			$askUpAmount = 0;

			$replyClickAmount = 0;
			$replyUpAmount = 0;

			$askIds = (new mThreadCategory)->where('target_type', mThreadCategory::TYPE_ASK )
							->where( 'category_id', $category_id )
							->where( 'status', '>', mThreadCategory::STATUS_DELETED )
							->select( 'target_id' )
							->get();
			if( !$askIds->isEmpty() ){
				$askClickAmount = (new mAsk)->whereIn('id', $askIds)
									->where('status', '>=', mAsk::STATUS_DELETED)
									->sum('click_count');
				$askUpAmount = (new mCount)->whereIn('target_id', $askIds )
										->where('type', mCount::TYPE_ASK)
										->where('status', '>=', mCount::STATUS_DELETED)
										->where('action', mCount::ACTION_UP)
										->count();
			}

			$replyIds = (new mThreadCategory)->where('target_type', mThreadCategory::TYPE_REPLY )
							->where( 'category_id', $category_id )
							->where( 'status', '>', mThreadCategory::STATUS_DELETED )
							->select( 'target_id' )
							->get();
			if( !$replyIds->isEmpty() ){
				$replyClickAmount = (new mReply)->whereIn('id', $replyIds)
									->where('status', '>=', mAsk::STATUS_DELETED)
									->sum('click_count');
				$replyUpAmount = (new mCount)->whereIn('target_id', $askIds )
										->where('type', mCount::TYPE_REPLY)
										->where('status', '>=', mCount::STATUS_DELETED)
										->where('action', mCount::ACTION_UP)
										->count();
			}


			$download_count = (new mDownload)->where('category_id')
						->where('status', '>=', mDownload::STATUS_DELETED)
						->count();

			$click_count = self::sumClickCount( $category_id );
			$counts = [
				'click_count' => $askClickAmount + $replyClickAmount,
				'uped_count'  => $askUpAmount + $replyUpAmount,
				'replies_count' => count($replyIds),
				'download_count' => $download_count,
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
