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
        $this->attributes['amount'] = $value;
    }

    public function count_user_reward($uid, $ask_id)
    {
        return $this->where('uid', $uid)->where('askid', $ask_id)->count();
    }

    public function count_ask_reward( $ask_id )
    {
        return $this->where('askid', $ask_id)->count();
    }
}
