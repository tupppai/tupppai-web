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

    public function create_reward($uid, $ask_id, $amount, $status = self::STATUS_NORMAL) {
        //记录打赏
        $reward = new self;
        $reward->uid    = $uid;
        $reward->askid  = $ask_id;
        $reward->amount = $amount;
        $reward->status = $status;
        $reward->save();
        
        return $reward;
    }

    public function update_status($reward_id, $status) {
        $reward = $this->find($reward_id);
        $reward->status = $status;
        $reward->save();

        return $reward;
    }

    public function count_user_reward($uid, $ask_id)
    {
        return $this->where('uid', $uid)->where('askid', $ask_id)->whereStatus(1)->count();
    }

    public function count_ask_reward( $ask_id )
    {
        return $this->where('askid', $ask_id)->whereStatus(1)->count();
    }
}
