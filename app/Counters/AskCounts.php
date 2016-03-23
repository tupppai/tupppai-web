<?php namespace App\Counters;

use App\Models\Ask as mAsk;
use Cache;

class AskCounts extends CounterBase {
    public static $key  = 'ask_';

    public static function _key($ask_id) {
        $key = self::__key(self::$key . $ask_id);

        return $key;
    }

    /**
     * 获取计数数据
     */
    public static function get($ask_id) {
        $key = self::_key($ask_id);

        return self::query($ask_id, function() use ( $ask_id ) {
            $mAsk   = new mAsk;
            $ask    = $mAsk->find($ask_id);
            $counts = [
				'reply_count'		   => $ask->reply_count,
				'click_count'          => $ask->click_count,
				'share_count'          => $ask->share_count,
				'weixin_share_count'   => $ask->weixin_share_count,
				'timeline_share_count' => $ask->timeline_share_count,
				'up_count'             => $ask->up_count,
				'comment_count'        => $ask->comment_count,
				'inform_count'         => $ask->inform_count,
            ];

            return self::put($ask_id, $counts );
        });
    }
    /**
     * 设置计数器
     */
    public static function put($ask_id, $val) {
        $key = self::_key($ask_id);
        Cache::put($key, $val, 1);//env('CACHE_LIFETIME'));
        return $val;
    }

    public static function query($ask_id, $closure = null) {
        $value = Cache::get(self::_key($ask_id), $closure);
        return $value;
    }

    public static function inc($ask_id, $field, $val = 1) {
		$field = $field.'_count';
        $counts = self::get($ask_id);
		$counts[$field] += $val;

        //todo 这里可以做uid筛选,一个人只算一次点击数
        //todo 50次存一次db
        if($counts[$field] % 50 == 0) {
            $ask = mAsk::find($ask_id);
            $ask->$field = $counts[$field];
            $ask->save();
        }

        return self::put($ask_id, $counts);
    }
}
