<?php


namespace App\Models;


class Reward extends ModelBase
{
    protected $table = 'rewards';
    protected $fillable = ['uid', 'askid', 'amount'];

    public function setAmountAttribute($value)
    {
        if (0 > $value) {
            return error('AMOUNT_ERROR','金额不能为负数');
        }
        $this->attributes['amount'] = $value * 1000;
    }

    public function getAmountAttribute($value)
    {
        return $value / 1000;
    }

    public static function get_user_reward_count($uid, $ask_id)
    {
        return self::where('uid', $uid)->where('askid', $ask_id)->count();
    }

    public static function get_ask_reward_count( $ask_id )
    {
        return self::where('askid', $ask_id)->count();
    }
}