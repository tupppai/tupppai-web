<?php
namespace App\Http\Controllers\Main2;
use App\Models\UserLanding as mUserLanding;
use App\Services\UserLanding as sUserLanding;
use App\Trades\Transaction as tTransaction;
use PingppLog;

class AccountController extends ControllerBase{

    /**
     * 微信充值
     */
    public function recharge() {
		$uid = $this->_uid;
		$amount = $this->post('amount', 'money');
		$type = $this->post('type', 'string', 'wx_pub');
		$data = [];

        $subject = '图派';
        $body    = '充值';
        $currency= 'cny';

        $attach  = array(
            'uid'=>$uid,
            'amount'=>$amount
        );
        $attach  = array_merge($attach, $data);
        $attach['type'] = $type;

        $extra   = array();
        $payment_type = tTransaction::PAYMENT_TYPE_CASH;
        if($type == 'wx') {
            $payment_type = tTransaction::PAYMENT_TYPE_WECHAT;
        }
        else if($type == 'wx_pub') {
            $payment_type = tTransaction::PAYMENT_TYPE_WECHAT;
            $user_landing = sUserLanding::getUserLandingByUid( $uid, mUserLanding::TYPE_WEIXIN_MP );
            if( !$user_landing ){
                return error('USER_LANDING_NOT_EXIST', '没有openid');
            }
            $data['open_id'] = $user_landing->openid;
            //todo: 找到open_id
            $extra['open_id'] = $data['open_id'];
        }
        else if($type == 'alipay') {
            $payment_type = tTransaction::PAYMENT_TYPE_ALIPAY;
        }

        $trade  = tTransaction::writeLog($uid, '', '', $payment_type, $amount, tTransaction::STATUS_PAYING, $subject, $body, $currency, $attach);

        \Pingpp\Pingpp::setApiKey(env('PINGPP_KEY'));
        $charge = \Pingpp\Charge::create(array(
            'order_no'  => $trade->trade_no,
            'amount'    => $amount,
            'app'       => array('id' => env('PINGPP_MP')),
            'channel'   => $type,
            'currency'  => $currency,
            'client_ip' => $trade->client_ip,
            'subject'   => $subject,
            'body'      => $body ,
            'extra'     => $extra
        ));
        PingppLog::addInfo( $charge );

        return $this->output($charge);
    }
}
