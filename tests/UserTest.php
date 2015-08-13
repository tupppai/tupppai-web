<?php

class UserTest extends TestCase
{

    public $type = 'android';

    public function testUserRegister(){

        $data = array(
            'type'       => 'mobile',
            'code'       => 'int',
            'mobile'     => "15018749411",
            'password'   => '123123',
            'nickname'   => 'nickname',
            'avatar'     => 'http://7u2spr.com1.z0.glb.clouddn.com/20150605-15425755715301a7625.jpg',
            'location'   =>  '',
            'city'       =>  10,
            'province'   =>  32,
            'sex'        => '0',
            'openid'     => '',
            'auth'       =>  '',
            'avatar_url' =>  ''
        );


        $res = $this->post('/v1/user/save', $data);

        $this->assertEquals( $res->data, '手机已注册');
    }

    public function testUserLogin(){
        $res = $this->get('/ask/index?size=15');

        if( empty($res->data) ) {
             $this->assertTrue(false);
        }

        $this->assertEquals( $res->data->uid, 1 );
        $this->assertEquals( $res->data->nickname, 'jq' );
        $this->assertEquals( $res->data->sex, 0 );
    }
}
