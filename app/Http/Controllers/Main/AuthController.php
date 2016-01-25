<?php namespace App\Http\Controllers\Main;

use App\Models\App;
use App\Models\ActionLog;

use App\Services\User;

class AuthController extends ControllerBase {

    public $_allow = array('*');

    // page index
    public function weixin() {
        $appid      = env('MP_APPID');
        $appsecret  = env('MP_APPSECRET');

        $code = $this->get('code');
        dd($this->http_get("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code"));

        return $this->output();
    }

    public function http_get($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        ob_start();
        curl_exec($ch);
        $result = ob_get_contents() ;
        ob_end_clean();

        return $result;
    }

}
