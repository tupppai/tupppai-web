<?php
namespace App\Models;

class ActionLog extends ModelBase
{
    private $table_prefix = 'action_log_';
    protected $connection = 'db_log';
    protected $table      = 'action_log_00';

    public function __construct() {
        parent::__construct();

        $this->table = $this->get_table();
    }

    private function get_table( $uid = null ){
        if( !$uid ){
            $uid = _uid();
        }
        $uid_mod = $uid %100;
        return $this->table_prefix.str_pad($uid_mod, 2, '0', STR_PAD_LEFT);
    }

    public function get_logs_by_uid($uid, $start_time = 0, $end_time = 99999999999){
        $this->table = $this->get_table( $uid );

        $data = self::selectRaw('oper_type, count(1) as num')
            #self::select('oper_type, count(1) as num')
            ->where('uid', $uid)
            ->where('create_time', '>', $start_time)
            ->where('create_time', '<', $end_time)
            ->groupBy('oper_type')
            ->lists('oper_type', 'num');

        return $data;
    }
}
