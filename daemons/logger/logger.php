<?php
require __DIR__ . '/../daemon.php';
require __DIR__ . '/helpers/log_base.php';

class Logger {
	private $daemon ;
    private $log_file ;
    private $config;

	public function init (){
        $this->config   = require __DIR__ . '/../../apps/config/database.php';
        $this->daemon	= new Daemon(true, "root", $this->config['daemon_log']);
	}

	public function save ($msgs){	
		$this->log_base->save($msgs);
	}

	public function start () {
        $platforms  = $this->config['platforms'];
		$types      = $this->config['types'];

		$keys = array();
		foreach($platforms as $platform){
			foreach($types as $type){
				$keys [] = $platform."_".$type;
			}
		}

        $this->daemon->start($this->config['daemon_num']);

        $this->log_base = new Log_base();
        $redis = new Redis();
        $redis->pconnect($this->config['redis']['host'], $this->config['redis']['port']);
        //$redis->auth($this->config['redis']['auth']);
        //find number in vim apps/library/cache/cache.php +8
		$redis->select(3);
        while(TRUE) {
			foreach($keys as $key) {
                if(!$this->log_base->init($key)){
                    continue;
                }
				$data = array();
				while($log = $redis->lPop($key)){
					$data[] = $log;
                }
                //todo if save false push back redis
				$this->save($data);
			}
			sleep(2);
		}
	}
}
$tof = new Logger();
$tof->init();
$tof->start();
