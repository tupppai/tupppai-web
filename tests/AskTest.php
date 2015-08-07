<?php

class AskTest extends TestCase
{

    public $type = 'android';

    public function testUserLogin(){
        $res = $this->get('/v1/ask/index?type=hot');

        if( empty($res->data) ) {
             $this->assertTrue(false);
        }

        $this->assertEquals( $res->data->uid, 1 );
        $this->assertEquals( $res->data->nickname, 'jq' );
        $this->assertEquals( $res->data->sex, 0 );
    }
}
