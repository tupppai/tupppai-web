<?php
require_once 'AndroidUMeng.php';
require_once 'iOSUMeng.php';

class Umeng {
    protected $platform = 'Android';

    private $android_umeng = null;
    private $ios_umeng = null;

    public function __construct(){
        if( $this->android_umeng ) {
             $this->android_umeng = new AndroidUMeng();
        }
        if( $this->ios_umeng ) {
             $this->ios_umeng = new iOSUMeng();
        }
    }

    public function push($text, $custom, $tokenList){
        dd(1);
        if( !empty( $tokenList['android']) ){
             $ret = $this->android_umeng->title(APP_NAME)
                ->ticker($text)
                ->text($text)
                ->listcast( $tokenList['android'] )
                ->after_open('go_custom')
                ->setContent('custom', json_encode($custom))
                ->send();
        }

        if( !empty( $tokenList['ios']) ){
             $ret = $this->ios_umeng->alert($text)
                ->listcast( $tokenList['ios'] )
                ->setContent('custom', json_encode($custom))
                ->send();
        }
    }
}
