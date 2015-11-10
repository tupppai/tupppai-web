<?php
namespace App\Models;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class UserScheduling extends ModelBase
{
    //这个status是个坑，后面估计也同步不了了,为了跟userScore保持一致
    const STATUS_NORMAL = 0;
    const STATUS_PAID   = 1;
    const STATUS_COMPLAIN = 2;
    const STATUS_DELETED  = 3;

    protected $table = 'user_schedulings';

    public function get_scheduling_by_id( $id ){
        return $this->where('id', $id )->first();
    }

    public function get_scheduling_by_uid($uid){
        $time = time();
        $scheduling = self::where('uid', $uid)
            ->where('end_time', '>', $time)
            ->where('start_time', '<=', $time)
            ->first();

        return $scheduling;
    }

    public function get_balance($uid) {
        $sum = self::where('uid', $uid)
            ->where('end_time', '<', time())
            ->selectRaw('status, sum(end_time-start_time) as sum')
            ->groupBy('status')
            ->lists('sum', 'status');

        $ret = array(0, 0);
        foreach($sum as $key=>$val) {
            $ret[$key] = $val;
        }
        return $ret;
    }
}
