<?php

namespace App\Models;

class UserSettlement extends ModelBase
{

    protected $table = 'user_settlements';
    const TYPE_PAID = 2;

    public function sum_total_score($operate_to=null) {
        $builder = self::where('status', self::STATUS_NORMAL);
        if( $operate_to )
            $builder = $builder->where('operate_to', $operate_to);
        return $builder->sum('score');
    }
}
