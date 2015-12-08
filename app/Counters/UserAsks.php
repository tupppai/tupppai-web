<?php
namespace App\Counters;

use App\Models\User as mUser;
use App\Models\Ask as mAsk;
use App\Services\Ask as sAsk;
use DB;

class UserAsks extends CounterBase {

    public static $key = 'counter_user_asks_';

    /**
     * 获取计数数据
     */ 
    public static function get($uid) {
        return self::query($uid, function() use ($uid) {
            $mAsk   = new mAsk;
            $table  = $mAsk->getTable();
            $count  = $mAsk->where(mAsk::_blocking($table, $uid))
                ->where('uid', $uid)
                ->count();

            return self::put($uid, $count);
        });
    }

}
