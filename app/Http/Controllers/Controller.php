<?php namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Response;
use Request, XssHtml;

class Controller extends BaseController
{
    protected function set($key, $val) {
        $_REQUEST[$key] = $val;
        $_POST[$key]    = $val;
        $_GET[$key]     = $val;
    }
    //
    protected function post($str, $type = 'normal', $default = null){
        $rlt = Request::input($str);
        $rlt = $this->valid($rlt, $type);
        if(is_null($rlt) and !is_null($default)){
            $rlt = $default;
            $_REQUEST[$str] = $default;
        }
        return $rlt;
    }

    protected function get($str, $type = 'normal', $default = null){
        $rlt = Request::input($str);
        $rlt = $this->valid($rlt, $type);
        if(is_null($rlt) and !is_null($default)){
            $rlt = $default;
            $_REQUEST[$str] = $default;
        }
        return $rlt;
    }

    protected function valid ($str, $type = 'normal') {
        if (is_array($str)) {
            foreach ($str as $k=>$v)
                if($type=='ip'){
                    $ip_seg = explode(',',$v);
                    if(count($ip_seg)<=0 ||count($ip_seg)>=3){
                        $str[$k]=null;
                    }elseif(count($ip_seg)==1){
                        $str[$k] = $this->valid($ip_seg[0]);
                    }elseif(count($ip_seg)==2){
                        $flag = !is_null($this->valid($ip_seg[0])) && !is_null($this->valid($ip_seg[1])) && (ip2long($ip_seg[0]) <=ip2long($ip_seg[1]));
                        $str[$k]= $flag ?$v:null;
                    }
                }else{
                    $str[$k] = $this->valid($v, $type);
                }
                if(is_null($str[$k])){
                    unset($str[$k]);
                }

            return $str;
        }

        $str = trim($str);

        switch ($type) {
            case 'alnum':
                return preg_match("/^[a-zA-Z0-9]+$/u",$str)? $str : NULL;
            case 'chinese':
                return preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u",$str)? $str : NULL;
            case 'integer':
            case 'number':
            case 'int':
                return (is_numeric($str) && $str < PHP_INT_MAX)? $str : NULL;
            case 'money':
                $str = str_replace(',', '', $str);
                return (is_numeric($str) && $str < PHP_INT_MAX)? $str : NULL;
            case 'phone':
                return preg_match("/^[0-9-]+$/u",$str)? $str : NULL;
            case 'username':
                return preg_match('/^[a-zA-Z][a-zA-Z0-9]{5,15}$/', $str)? $str: NULL;
            case 'email':
                return filter_var($str, FILTER_VALIDATE_EMAIL)? $str : NULL;
            case 'float':
                return filter_var($str, FILTER_VALIDATE_FLOAT)? $str : NULL;
            case 'url':
                return filter_var($str, FILTER_VALIDATE_URL)? $str : NULL;
            case 'ip':
                return filter_var($str, FILTER_VALIDATE_IP)? $str : NULL;
            case 'richbox':
                return strtr($str, '\'"', '‘“');
                break;
            case 'date':
                return strtotime($str)? date('Y-m-d H:i:s', strtotime($str)) : NULL;
                break;
            //customized
            case 'mobile':
                return preg_match("/^1[3|5|7|8|9][0-9]{9}$/u", $str)? $str : NULL;
            case 'json_array':
                $arr = json_decode($str);
                if(!empty($arr))
                    return $arr;
                return NULL;
            case 'json':
                return preg_match('/[^,:{}\\[\\]0-9.\-+Eaeflnr-u \n\r\t]/',$str)? $str: NULL;
            case 'emoji':
                return preg_match("/[\xF0-\xF7][\x80-\xBF]{3}/", $str)? $str: NULL;
            case 'normal':
            case 'string':
            default:
                //特殊处理
                if( $str === '0' ){
                    return $str;
                }
                $xss = new XssHtml($str);
                $str = $xss->getHtml();

                return $str? $str: NULL;
                //return preg_match("/^[^'\"<>]+$/u",$str)? $str : NULL;
        }
    }

    public $_code   = 0;
    public $_of     = 'html';

    public function set_code($_code = 0){
        $this->_code = $_code;

        return $this;
    }

    public function set_of($_of = 'html'){
        $this->_of = $_of;

        return $this;
    }

    public function output($data = array(), $info = ''){
        if(!$this->_of) {
            if(Request::ajax()) {
                $this->_of = 'json';
            }
            if( isset($_REQUEST['_of']) ){
                $this->_of = $_REQUEST['_of'];
            }
        }

        switch( $this->_of ) {
        case 'html':
            return $this->output_html($data, $info);
        case 'table':
            return $this->output_table($data, $info);
        case 'json':
        default:
            return $this->output_json($data, $info);
        }
    }

    public function output_html( $data = array(), $info = '' ){}
    public function output_table( $data = array(), $info = '' ){}
    public function output_json( $data = array(), $info = '' ){
        $data = json_format(1, $this->_code, $data, $info);
        #return $data;
        return response()->json( $data );
    }
}
