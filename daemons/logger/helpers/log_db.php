<?php

class Log_db {
    
    static $ins = array(); //单例
	private $type;
    private $platform;
    private $r;
    private $table_prefix = 'action_log_';

	public $con;

    const TYPE_ACTION = 0x1;
    
    public static function getInstance($db = self::TYPE_ACTION) {
        if (!isset(self::$ins[$db])) {
            self::$ins[$db] = new self($db);
        }
        return self::$ins[$db];
    }
    
    private function __construct($db) {
        $this->config   = require __DIR__ . '/../../../apps/config/database.php';

        $this->con = mysql_connect(
        //$this->con = mysql_pconnect(
            $this->config['database_log']['host'],
            $this->config['database_log']['username'],
            $this->config['database_log']['password']
        );
        if (!$this->con) {
            printf("%s\tplatform\t%s\ttype\t%s\terror\t%s\n", date("c"), $this->platform, $this->type, mysql_error());
            return null;
        }

        //mysql_select_db($this->config['database_log']['dbname'], $this->con); 
        mysql_select_db($this->config['database_log']['dbname']); 
        return $this->con;
    }

    public function init($platform, $type){
		$this->platform = $platform;
        $this->type	    = $type;
        
        if (!$this->con) {
            printf("%s\tplatform\t%s\ttype\t%s\terror\t%s\n", date("c"), $this->platform, $this->type, mysql_error());
            return false;
        }
        return true;
	}

	// formatted log LIKE: date("Y-m-d H:i:s")."|".$obj_log."\n";
	public function save($logs) {
		if(empty($logs)){
			return false;
        }
        $data = array();
        foreach($logs as $log) {
            $log = json_decode($log);
            $data[$log->uid][] = "('', {$log->uid}, '{$log->ip}', '{$log->uri}', '{$log->oper_type}', '{$log->data}', '{$log->info}', {$log->create_time})";
        }

        foreach($data as $uid => $row){
            $table_name = $this->table_prefix.str_pad($uid %100, 2, '0', STR_PAD_LEFT);
            $sql = "INSERT INTO $table_name (id, uid, ip, uri, oper_type, data, info, create_time) VALUES ";

            $sql .= implode(",", $row) . ";";

            mysql_query($sql);
            //mysql_query($sql, $this->con);
        }
        //mysql_close($this->conn);
		return true;
	}
	
    protected function close ($result) {
        //mysql_free_result($result);
        //关闭连接
        mysql_close();
	}
}
