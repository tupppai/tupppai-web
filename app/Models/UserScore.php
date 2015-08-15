<?php
namespace App\Models;

class UserScore extends ModelBase
{
    //这个status是个坑，后面估计也同步不了了
    const STATUS_NORMAL = 0;
    const STATUS_PAID   = 1;
    const STATUS_COMPLAIN = 2;
    const STATUS_DELETED  = 3;

    protected $table = 'user_scores';

    public function get_user_score($type, $item_id, $uid=null) {
        $builder = where('type', $type)
            ->where('item_id', $item_id);
        if($uid) {
            $builder = self::where('uid', $uid);
        }

        $user_score = $builder->first();
        return $user_score;
    }

    public function get_balance($uid){
        $sum = self::where('uid', $uid)
            ->selectRaw('status, sum(score) as sum')
            ->groupBy('status')
            ->lists('sum', 'status')
            ->toArray();

        $ret = array(0, 0);
        foreach($sum as $key=>$val) {
            $ret[$key] = $val;
        }
        return $ret;
    }

    public function sum_scores_by_operuid($uid) {
        $sum = self::where('oper_by', $uid)
            ->selectRaw('sum(score) as sum')
            ->pluck('sum');

        return $sum;
    }
    
    public function avg_scores_by_operuid($uid) {
        $sum = self::where('oper_by', $uid)
            ->selectRaw('avg(score) as avt')
            ->pluck('avg');

        return $sum;
    }
    
    public function avg_scores_by_uid($uid) {
        $sum = self::where('uid', $uid)
            ->selectRaw('avg(score) as avt')
            ->pluck('avg');

        return $sum;
    }

    public function count_passed_replies($uid){
        return self::where('uid', $uid)
            ->where('type', self::TYPE_REPLY)
            ->where('content', '')
            ->count();
    }

    public function count_rejected_replies($uid){
        return self::where('uid', $uid)
            ->where('type', self::TYPE_REPLY)
            ->where('content', '!=', '')
            ->count();
    }

    public function pay_score($uid){
        return self::where('uid', $uid)
            ->where('status', self::STATUS_NORMAL)
            ->update(array('status'=>self::STATUS_PAID));
    }
}
