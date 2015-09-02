<?php

class Daemon{
 
    private $info_dir="/tmp";
    private $pid_file="";
    private $terminate=false; 
    private $workers_count=0;
    private $gc_enabled=null;
    private $workers_max=8; 
    public function __construct($is_sington=false,$user='nobody',$output="/dev/null"){
        $this->is_sington=$is_sington; 
        $this->user=$user;
        $this->output=$output;
        $this->checkPcntl();
    }

    public function get_workers_count (){
	    return $this->workers_count;
    }
	
    public function checkPcntl(){
        if ( ! function_exists('pcntl_signal_dispatch')) {
            // PHP < 5.3 uses ticks to handle signals instead of pcntl_signal_dispatch
            // call sighandler only every 10 ticks
            declare(ticks = 10);
        }
 
        // Make sure PHP has support for pcntl
        if ( ! function_exists('pcntl_signal')) {
            $message = 'PHP does not appear to be compiled with the PCNTL extension.  This is neccesary for daemonization';
            $this->_log($message);
            throw new Exception($message);
        }
		
        pcntl_signal(SIGTERM, array(__CLASS__, "signalHandler"),false);
        pcntl_signal(SIGINT, array(__CLASS__, "signalHandler"),false);
        pcntl_signal(SIGQUIT, array(__CLASS__, "signalHandler"),false);
 
        // Enable PHP 5.3 garbage collection
        if (function_exists('gc_enable'))
        {
            gc_enable();
            $this->gc_enabled = gc_enabled();
        }
    }
 
    // daemon
    public function daemonize(){
 
        global $stdin, $stdout, $stderr;
        global $argv;
 
        set_time_limit(0);
 
        if (php_sapi_name() != "cli"){
            die("only run in command line mode\n");
        }
 
        if ($this->is_sington==true){
 
            $this->pid_file = $this->info_dir . "/" .__CLASS__ . "_" . substr(basename($argv[0]), 0, -4) . ".pid";
            $this->checkPidfile();
        }
 
        umask(0); 
        if (pcntl_fork() != 0){ 
            exit();
        }
 
        posix_setsid();
        if (pcntl_fork() != 0){    
            exit();
        }
 
        chdir("/");
 
        $this->setUser($this->user) or die("cannot change owner");
 
        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);
 
        $stdin  = fopen($this->output, 'r');
        $stdout = fopen($this->output, 'a');
        $stderr = fopen($this->output, 'a');
 
        if ($this->is_sington==true){
            $this->createPidfile();
        }
 
    }
    
    public function checkPidfile(){
 
        if (!file_exists($this->pid_file)){
            return true;
        }
        $pid = file_get_contents($this->pid_file);
        $pid = intval($pid);
        if ($pid > 0 && posix_kill($pid, 0)){
            $this->_log("the daemon process is already started");
        }
        else {
            $this->_log("the daemon proces end abnormally, please check pidfile " . $this->pid_file);
        }
        exit(1);
 
    }
    
    public function createPidfile(){
 
        if (!is_dir($this->info_dir)){
            mkdir($this->info_dir);
        }
        $fp = fopen($this->pid_file, 'w') or die("cannot create pid file");
        fwrite($fp, posix_getpid());
        fclose($fp);
        $this->_log("create pid file " . $this->pid_file);
    }
 
    public function setUser($name){
 
        $result = false;
        if (empty($name)){
            return true;
        }
        $user = posix_getpwnam($name);
        if ($user) {
            $uid = $user['uid'];
            $gid = $user['gid'];
            $result = posix_setuid($uid);
            posix_setgid($gid);
        }
        return $result;
 
    }
    
    public function signalHandler($signo){
 
        switch($signo){
 
            case SIGUSR1: //busy
            if ($this->workers_count < $this->workers_max){
                $pid = pcntl_fork();
                if ($pid > 0){
                    $this->workers_count ++;
                }
            }
            break;
            case SIGCHLD:
                while(($pid=pcntl_waitpid(-1, $status, WNOHANG)) > 0){
                    $this->workers_count --;
                }
            break;
            case SIGTERM:
            case SIGHUP:
            case SIGQUIT:
                $this->terminate = true;
            break;
            default:
            return false;
        }
 
    }

    /**
     * start a daemon
     */
    public function start($count=1){
	
		$this->daemonize();
 
        $this->_log("daemon process is running now");
        pcntl_signal(SIGCHLD, array(__CLASS__, "signalHandler"),false); // if worker die, minus children num
        while (true) {
            if (function_exists('pcntl_signal_dispatch')){
                pcntl_signal_dispatch();
            }
 
            if ($this->terminate){
                break;
            }
            $pid=-1;
            if($this->workers_count<$count){
                $pid=pcntl_fork();
            }
 
            if($pid>0){
                $this->workers_count++;
 
            }elseif($pid==0){
                pcntl_signal(SIGTERM, SIG_DFL);
                pcntl_signal(SIGCHLD, SIG_DFL);
                return;
            }else{
                sleep(2);
            }
 
 
        }
 
        $this->mainQuit();
        exit(0);
    }
 
    public function mainQuit(){
 
        if (file_exists($this->pid_file)){
            unlink($this->pid_file);
            $this->_log("delete pid file " . $this->pid_file);
        }
        $this->_log("daemon process exit now");
        posix_kill(0, SIGKILL);
        exit(0);
    }
 
    private  function _log($message){
        printf("%s\t%d\t%d\t%s\n", date("c"), posix_getpid(), posix_getppid(), $message);
    }
 
}
 
