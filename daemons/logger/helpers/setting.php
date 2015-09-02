<?php
class Setting {
	const SERVER_LOG_PATH   = "/data/logs/lpop_log";
	const REDIS_HOST = '10.177.137.231';
	const REDIS_PORT = '6789';
	const REDIS_AUTH = 'd9ca267f272c4837848d465858e5fd29';
    const DAEMON_COUNT      = 3;


	const LOG_PATH          = '/data/logs/platform/';
	const FILE_MAX_SIZE     = 1099511627776; //1G
	const LOG_MAX_LENGTH    = 102400; //100K
	const BUFFER_LENGTH     = 1024; //1k

	public static function platforms (){
		return array('api','pc','admin');
	}

	public static function types (){
		return array('sys','ajax','err','user','debug');
	}
}
?>
