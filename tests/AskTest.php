<?php

class AskTest extends TestCase
{

    public $type = 'android';

    public function testHotAsks(){

        $res2 = $this->get('/v1/ask/index?type=hot');
        var_dump( $res2);
        $arr2 = json_decode( json_encode( $res2 ), true );
        $ids2 = array_column( $arr2['data'], 'id');

        $this->assertTrue( true );
    }

    public function testNewAsks(){
        $res = $this->get('/v1/ask/index?type=new');
        var_dump($res);
        $arr = json_decode( json_encode( $res ), true );
        $ids = array_column( $arr['data'], 'id');

        $this->assertTrue( true );
    }

}
