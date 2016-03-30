<?php namespace App\Counters;

use App\Models\Inform as mInform;
use DB;

class AskInforms extends CounterBase {

    public static $key  = 'ask_informs_';
    
    public static function _key($ask_id) {
        $key = self::$key . $ask_id ;

        return $key;
    }

    /**
     * 获取计数数据
     */ 
    public static function get($ask_id) {
        $key = self::_key($ask_id);

        return self::query($key, function() use ($key, $ask_id) {
            $mInform  = new mInform;
            $count    = $mInform->where('target_type', mInform::TYPE_ASK)
                ->where('target_id', $ask_id)
                ->valid()
                ->count();

            return self::put($key, $count);
        });
    }

    public static function inc($ask_id, $val = 1) {
        self::get($ask_id);

        return self::increment(self::_key($ask_id), $val);
    }
}
