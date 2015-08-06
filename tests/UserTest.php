<?php

class UserTest extends TestCase
{

    public $type = 'android';

    public function testUserLogin(){
        $res = $this->get('/v1/user/login');
        dd($res);
        $this->assertEquals( true );
    }
}
