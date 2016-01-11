<?php

namespace App\Models;

class UserSettlement extends ModelBase
{
    protected $table = 'user_settlements';

    public function sum_total_score($operate_to=null) {
        $builder = self::where('status', self::STATUS_NORMAL);
        if( $operate_to )
            $builder = $builder->where('operate_to', $operate_to);
        return $builder->sum('score');
    }

    public function pay( $uid, $operate_to, $operate_type, $score_item, $score, $data ){
        $this->uid=$uid;
        $this->operate_to=$operate_to;
        $this->operate_type=$operate_type;
        $this->score_item=$score_item;
        $this->score=$score;
        $this->data=$data;
        return $this->save();
    }
}
