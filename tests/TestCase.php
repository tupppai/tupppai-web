<?php

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    public $type = 'android';
    

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function get($uri, array $headers = array()) {
        $this->addRoute('GET', $uri);

        $response = parent::get($uri, $headers);
        #todo: check return code & html format

        return json_decode($response->response->getContent());
    }

    public function post($uri, array $data = array(), array $headers = array()) {
        $this->addRoute('POST', $uri);

        $response = parent::post($uri, $data, $headers);
        #todo: check return code & html format

        return json_decode($response->response->getContent());
    }

    public function addRoute($method, $uri) {
        $type= $this->type;
        $uri = explode("?", $uri)[0];
        $uri = explode("&", $uri)[0];

        $namespace  = "\App\Http\Controllers\\".ucfirst($type)."\\";
        $segments   = explode('/', trim($uri, '/'));
        $name       = $namespace.ucfirst($segments[0]);
        $action     = $segments[1];
        $path       = '/';

        if( isset($segments[2]) ) {
            $segments[2] = '{id}';
        }
        $path .= implode("/", $segments);
        
        app()->addRoute($method, $path, "{$name}Controller@{$action}Action");
    }
}
