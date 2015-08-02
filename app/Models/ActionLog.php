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
        $table = $this->get_table( $uid );

        $log = new self;
        $sql = 'select t.oper_type, count(1) as num'.
            ' FROM '.$table.' t'.
            ' WHERE t.uid ='.$uid.' AND create_time>'.$start_time.' AND create_time<'.$end_time.
            ' GROUP BY t.oper_type';

        return $logs = new Resultset(null, $log, $log->getReadConnection()->query($sql));
    }
}
