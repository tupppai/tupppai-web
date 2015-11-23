<?php
require_once 'AndroidUMeng.php';
require_once 'iOSUMeng.php';

class Umeng {
    protected $platform = 'Android';

    private $android_umeng = null;
    private $ios_umeng = null;
    private $title     = APP_NAME;

    public function __construct(){
        if( !$this->android_umeng ) {
            $this->android_umeng = new AndroidUMeng();
            $this->android_umeng->title(APP_NAME);
        }
        if( !$this->ios_umeng ) {
            $this->ios_umeng = new iOSUMeng();
        }
    }

    public function title($title){
        $this->title = $title;
        $this->android_umeng->title($title);

        return $this;
    }

    public function push($data, $custom = array()){
        $ret = null;

        if( !empty( $data['token']['android']) ){
             $ret = $this->android_umeng
                 ->ticker($data['text'])
                 ->text($data['text'])
                 ->listcast( $data['token']['android'] )
                 ->after_open('go_custom')
                 ->setContent('custom', json_encode($custom))
                 ->send();
        }

        if( !empty( $data['token']['ios']) ){
             $ret = $this->ios_umeng->alert($data['text'])
                 ->listcast( $data['token']['ios'] )
                 ->badge($custom['count'])
                 ->setExtra($custom)
                 ->send();
        }

        return $ret;
    }
}
