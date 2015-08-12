<?php

class AskTest extends TestCase
{

    public $type = 'android';

    public function testHotAsks(){
        $res = $this->get('/ask/index?type=hot');
        dd($res);

        $this->assertTrue( true );
    }

    public function testNewAsks(){
        $res = $this->get('/ask/index?type=new');
        dd($res);

        $this->assertTrue( true );
    }
}
