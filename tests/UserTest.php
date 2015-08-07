<?php

class UserTest extends TestCase
{

    public $type = 'android';

    public function testUserLogin(){
        $res = $this->get('/v1/user/login');

        $this->assertEquals( $res->data->uid, 1 );
        $this->assertEquals( $res->data->nickname, 'jq' );
        $this->assertEquals( $res->data->sex, 0 );
    }
}
