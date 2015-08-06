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

    private $namespaces = array(
        'android'=>'App\Http\Controllers\Android\\',
        'admin'=>'App\Http\Controllers\Admin\\'
    );

    public function get($uri, array $headers = array()) {
        set_router($uri, $this->type);

        $response = parent::get($uri, $headers);
        #todo: check return code & html format

        return $response->response->getContent();
    }
    
    public function post($uri, array $data = array(), array $headers = array()) {
        set_router($uri, $this->type);

        $response = parent::post($uri, $headers);
        #todo: check return code & html format
       
        return $response->response->getContent();
    }
}
