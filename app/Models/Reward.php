<?php


namespace App\Models;


class Reward extends ModelBase
{
    protected $table = 'rewards';
    protected $fillable = ['uid', 'target_id', 'target_type', 'amount'];

    public function setAmountAttribute($value)
    {
        if (0 > $value) {
            return error('AMOUNT_ERROR','金额不能为负数');
        }
        $this->attributes['amount'] = $value;
    }

    public function create_reward($uid, $target_type, $target_id, $amount, $status = self::STATUS_NORMAL) {
        //记录打赏
        $reward = new self;
        $reward->uid    = $uid;
        $reward->target_id   = $target_id;
        $reward->target_type = $target_type;
        $reward->amount = $amount;
        $reward->status = $status;
        $reward->save();

        return $reward;
    }

    public function create_ask_reward( $uid, $askid, $amount, $status = self::STATUS_NORMAL ){
        return $this->create_reward( $uid, self::TYPE_ASK, $askid, $amount, $status );
    }

    public function create_reply_reward( $uid, $replyid, $amount, $status = self::STATUS_NORMAL ){
        return $this->create_reward( $uid , self::TYPE_REPLY, $replyid, $amount, $status );
    }

    public function update_status($reward_id, $status) {
        $reward = $this->find($reward_id);
        $reward->status = $status;
        $reward->save();

        return $reward;
    }

    public function count_user_reward_by_target($uid, $target_type, $target_id)
    {
        return $this->where('uid', $uid)
                    ->where('target_type', $target_type)
                    ->where('target_id', $target_id)
                    ->valid()
                    ->count();
    }

    public function count_user_ask_reward_id( $uid, $ask_id ){
        return $this->count_user_reward_by_target( $uid, self::TYPE_ASK, $ask_id );
    }
    public function count_user_reply_reward_id( $uid, $reply_id ){
        return $this->count_user_reward_by_target( $uid, self::TYPE_REPLY, $reply_id );
    }

    public function count_reward_by_target( $target_type, $target_id ){
        return $this->where('target_id', $target_id)
                ->where('target_type', $target_type)
                ->valid()
                ->count();
    }
    public function count_ask_reward_by_id( $target_id )
    {
        return $this->count_reward_by_target( self::TYPE_ASK, $target_id );
    }

    public function count_reply_reward_by_id( $target_id )
    {
        return $this->count_reward_by_target( self::TYPE_REPLY, $target_id );
    }
}
