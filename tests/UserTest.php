<?php

class UserTest extends TestCase
{

    public $type = 'android';

    public function testUserLogin(){
        $res = $this->get('/v1/user/login');
        $return = json_decode( $res );

        $this->assertEquals( $return->data->uid, 1 );
        $this->assertEquals( $return->data->nickname, 'jq' );
        $this->assertEquals( $return->data->sex, '男' );
    }
}
