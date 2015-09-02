<?php
require __DIR__ . '/log_file.php';
require __DIR__ . '/log_db.php';

class Log_base {
	public $fp;

    public $log_file;
    public $log_db;

	public function __construct(){
        $this->log_file = new Log_file();
        $this->log_db   = Log_db::getInstance();
    }

    public function init($key){
        $arr = explode("_", $key);
		if(sizeof($arr) != 2)
            return false;

        $this->platform   = $arr[0];
        $this->type       = $arr[1];

        if($this->platform == 'db' && $this->type == 'user'){
            return $this->log_db->init($this->platform, $this->type);
        }
        else {
            return $this->log_file->init($this->platform, $this->type);
        }
    }

	public function save($logs) {
		if(empty($logs)){
			return false;
		}
        //printf("%s\tplatform\t%s\tlogcount\t%d\n", date("c"), $this->platform, sizeof($logs));
        printf("%s\t%s\t%s\tnum:\t%d\n", date("c"), $this->platform, $this->type, sizeof($logs));

        if($this->platform == 'db' && $this->type == 'user'){
            $this->log_db->save($logs);
        }
        else {
            $this->log_file->save($logs);
        }
	}
}
