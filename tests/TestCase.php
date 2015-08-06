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

        return parent::get($uri, $headers);
    }
    
}
