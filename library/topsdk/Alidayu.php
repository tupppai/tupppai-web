<?php 
//include __DIR__ . DIRECTORY_SEPARATOR . "TopSdk.php";
define('TOP_SDK_WORK_DIR', '/tmp');

class Alidayu {

    protected $c    = null;

    public function __construct($appkey, $secret) {
        //date_default_timezone_set('Asia/Shanghai');
        $client = new TopClient();
        $client->appkey     = $appkey; 
        $client->secretKey  = $secret; 

        $this->c    = $client;
        $this->req  = new AlibabaAliqinFcSmsNumSendRequest;
    }

    public function send($phone, $code) {
        $req = $this->req;
        //todo 频率限制

        //公共回传参数，在“消息返回”中会透传回该参数；举例：用户可以传入自己下级的会员ID
        //，在消息返回时，该会员ID会包含在内，用户可以根据该会员ID识别是哪位会员使用了你的应用
        $req->setSmsType("normal");   //短信类型，传入值请填写normal
        $req->setSmsFreeSignName("注册验证");//短信签名,等图派审核通过替换成图派
        $req->setSmsParam("{\"code\":\"$code\",\"product\":\"图派\"}");  
        //短信模板变量，传参规则{"key":"value"}，
        //key的名字须和申请模板中的变量名一致
        //，多个变量之间以逗号隔开。示例：针对模板“验证码${code}，
        //您正在进行${product}身份验证，打死不要告诉别人哦！”，传参时需传入{"code":"1234","product":"alidayu"}
        $req->setRecNum($phone);  //用户的手机号码
        $req->setSmsTemplateCode("SMS_3525360");//短信模板，等短信模板审核通过替换ID,可以先用

        $resp = $this->c->execute($req);
        
        return $resp;
    }
}
?>
