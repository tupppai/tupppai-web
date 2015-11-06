<?php
namespace App\Models;

use DB;

class UserScore extends ModelBase
{
    //这个status是个坑，后面估计也同步不了了
    const STATUS_NORMAL = 0;
    const STATUS_PAID   = 1;
    const STATUS_COMPLAIN = 2;
    const STATUS_DELETED  = 3;

    protected $table = 'user_scores';

    public function get_user_score($type, $item_id, $uid=null) {
        $builder = $this->where( ['type'=> $type,'item_id'=> $item_id] );
        if($uid) {
            $builder = $builder->where('uid', $uid);
        }
        $user_score = $builder->first();

        return $user_score;
    }

    public function get_balance($uid){
        $sum = $this->where('uid', $uid)
            ->selectRaw('status, sum(score) as sum')
            ->groupBy('status')
            ->get();

        $ret = array(0, 0);
        foreach($sum as $row) {
            $ret[$row->status] = $row->sum;
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

    public function get_stat( $uid ){
        //统计
        $phql  = 'SELECT count( CASE WHEN (UNIX_TIMESTAMP()-action_time<60*60*24) AND score>0 THEN id END) as today_passed,';
        $phql .= ' count( CASE WHEN ( UNIX_TIMESTAMP()-action_time>60*60*24*2 AND (UNIX_TIMESTAMP()-action_time)<60*60*24 and score>0) THEN id END) as yesterday_passed,';
        $phql .= ' count( CASE WHEN ( UNIX_TIMESTAMP()-action_time<60*60*24*7  and score>0) THEN id END ) as last7days_passed,';
        $phql .= ' count( CASE WHEN (UNIX_TIMESTAMP()-action_time<60*60*24) AND score<=0 THEN id END) as today_denied,';
        $phql .= ' count( CASE WHEN ( UNIX_TIMESTAMP()-action_time>60*60*24*2 AND (UNIX_TIMESTAMP()-action_time)<60*60*24 and score<=0) THEN id END) as yesterday_denied,';
        $phql .= ' count( CASE WHEN ( UNIX_TIMESTAMP()-action_time<60*60*24*7  and score<=0) THEN id END ) as last7days_denied,';
        $phql .= ' count( id ) as total,';
        $phql .= ' count( CASE WHEN score>0 THEN id END) as passed,';
        $phql .= ' count( CASE WHEN score=0 THEN id END) as denied';
        $phql .= ' FROM user_scores where uid='.$uid.' group by uid';

        return DB::select( $phql);
    }
}
