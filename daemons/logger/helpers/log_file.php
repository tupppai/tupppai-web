<?php

class Log_file {
	public $fp;
	public $type;
    public $platform;
    public $filename;

    public function __construct(){
        $this->config   = require __DIR__ . '/../../../apps/config/database.php';
    }

	public function init($platform, $type){
		$this->platform = $platform;
        $this->type	    = $type;
		$this->filename = $this->get_file_name($this->config['log_path'] . $this->platform . "/".$this->type."_" . date('Ymd') . '.log');

        if(!file_exists($this->filename)){
            $this->fp = $this->open_file($this->filename);
            if(!$this->fp) {
                printf("%s\tplatform\t%s\ttype\t%s\terror\n", date("c"), $this->platform, $this->type);
                return false;
            }
		    $this->close_file($this->fp);
            return true;
        }
        return true;
    }

	// formatted log LIKE: date("Y-m-d H:i:s")."|".$obj_log."\n";
	public function save($logs) {
		if(empty($logs)){
			return false;
		}

		$this->fp = $this->open_file($this->filename);
		if (!$this->fp) {
            printf("%s\tplatform\t%s\ttype\t%s\terror\n", date("c"), $this->platform, $this->type);
			return false;
		}

		$str = '';
        foreach($logs as $log) {
            $str .= "[".date("Y-m-d H:i:s")."][API] - $log \n";
        }

		fwrite($this->fp, $str, $this->config['log_max_length']);
		$this->close_file($this->fp);
		return true;
	}
	
	protected function get_file_name ($filename) {
		if (file_exists($filename) && $this->config['file_max_size'] <= $this->get_file_size($filename)) {
	
			$idx = 0;
			do {
				$tmpname = $filename . sprintf('.%03d', $idx++);
			} while(file_exists($tmpname) && $idx < 1000);
	
			@rename($filename, $tmpname);
		}
		return $filename;
	}
	
	protected function open_file ($filename) {
		$this->fp = fopen($filename, 'a');
	
		$start_time = microtime(TRUE);
		while (false == flock($this->fp, LOCK_EX | LOCK_NB) && microtime(TRUE)-$start_time > 2 ) {
			usleep(round(rand(0, 100)*1000));
		}
	
		if (!flock($this->fp, LOCK_EX | LOCK_NB)) {
			return NULL;
		}
	
		return $this->fp;
	}
	
	protected function close_file ($fp) {
		flock($fp, LOCK_UN);
		fclose($fp);
	}
	
	protected function get_file_size ($filename) {
		clearstatcache();
		return filesize($filename);
	}
}
