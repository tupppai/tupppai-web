<?php

class UserTest extends TestCase
{
    public function testUserLogin(){
        $response = $this->action('GET', 'UserController@login');
        $return = json_decode( $response->getContent() );
        echo( $response->getContent() );
        //$this->assertEquals( $return['uid'], 1 );
    }
}
