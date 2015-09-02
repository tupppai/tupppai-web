<?php

class Logger {
    private $logger;

    public function __construct() {
        $host       = app()->request->getHost();
        $hostname   = hostmaps($host);

        $this->logger = new Logger($hostname, [$this->getMonologHandler()]);
    }

    protected function getMonologHandler() {
        return (new StreamHandler(storage_path('logs/lumen.log'), Logger::DEBUG))
                    ->setFormatter(new LineFormatter(null, null, true, true));
    }

    public function log($message, array $context = array()) {
        $ip         = app()->request->ip();
        $method     = app()->request->method();
        $path       = app()->request->path();
        $ajax       = app()->request->ajax();

        $query      = app()->request->query();
        if(!empty($_POST)) {
            $query = array_merge($_POST, $query);
        }
        $query = array_merge($context, $query);

        $_uid  = session('uid');
        #todo: 异步日志
        $this->logger->info("[$method][$ajax][$ip][$path][$_uid]", $query);
    }
}
