<?php namespace App\Http\Controllers\Api;

use App\Services\User as sUser;
use App\Services\UserLanding as sUserLanding;

use App\Trades\Transaction as tTransaction;
use App\Trades\User as tUser;
use App\Trades\Account as tAccount;

use App\Models\UserLanding as mUserLanding;

use Log;

class MoneyController extends ControllerBase{

    public $open_id;

    public function __construct() {
        parent::__construct();
        $landing = sUserLanding::getUserLandingByUid($this->_uid, mUserLanding::TYPE_WEIXIN);
        if (!$landing || $landing->status != mUserLanding::STATUS_NORMAL) {
            return error('OPEN_ID_NOT_EXIST', '未绑定微信账号');
        }
        $this->open_id = $landing->openid;

        \Pingpp\Pingpp::setApiKey(env('PINGPP_KEY'));
    }


    /**
     * 充值
     */
    public function chargeAction() {
        $type    = $this->post('type', 'int', 'wx');
        $amount  = $this->post('amount', 'float');

        $open_id = $this->open_id;
        $subject = '图派';
        $body    = '充值';
        $currency= 'cny';

        $trade  = tTransaction::writeLog($this->_uid, '', '', tTransaction::PAYMENT_TYPE_WECHAT, $amount, tTransaction::STATUS_PAYING, $subject, $body, $currency);
        $account= tUser::addBalance($this->_uid, $amount, "$subject-$body", "$open_id-".$trade->id);
       
        $charge = \Pingpp\Charge::create(array(
            'order_no'  => $trade->id,
            'amount'    => $amount,
            'app'       => array('id' => env('PINGPP_OP')),
            'channel'   => $type,
            'currency'  => 'cny',
            'client_ip' => $trade->client_ip,
            'subject'   => $subject,
            'body'      => $body 
        ));
        return $this->output($charge);
    }

    /**
     * 红包提现
     */
    public function redAction() {
        $amount  = $this->post('amount', 'money');

        $user    = $this->checkAmount($amount, mUserLanding::TYPE_WEIXIN);

        $open_id = $this->open_id;
        $subject = '图派';
        $body    = '红包提现';
        $currency= 'cny';

        $trade = tTransaction::writeLog($this->_uid, '', '', tTransaction::PAYMENT_TYPE_WECHAT_RED, $amount, tTransaction::STATUS_PAYING, $subject, $body, $currency);
        $account = tUser::reduceBalance($this->_uid, $amount, "$subject-$body", "$open_id-".$trade->id);

        $red = \Pingpp\RedEnvelope::create(
            array(
                'order_no'    => $trade->id,
                'app'         => array('id' => env('PINGPP_OP')),
                'channel'     => 'wx', 
                'amount'      => $amount,
                'currency'    => $currency,
                'subject'     => $subject,
                'body'        => $body,
                'extra'       => array(
                    'nick_name' => '图派',
                    'send_name' => '皮埃斯网络科技'
                ),//extra 需填入的参数请参阅 API 文档
                'recipient'   => $open_id,
                'description' => '图派红包提现,绽放你的灵感'
            )
        );

        return $this->output($red);
    }

    /**
     * 企业转账
     */
    public function transferAction() {
        $amount  = $this->post('amount', 'money');

        $user    = $this->checkAmount($amount, mUserLanding::TYPE_WEIXIN);

        $open_id = $this->open_id;
        $subject = '图派';
        $body    = '红包提现';
        $currency= 'cny';

        $trade = tTransaction::writeLog($this->_uid, '', '', tTransaction::PAYMENT_TYPE_WECHAT_TRANSFER, $amount, tTransaction::STATUS_PAYING, $subject, $body, $currency);
        $account = tUser::reduceBalance($this->_uid, $amount, "$subject-$body", "$open_id-".$trade->id);

        $trans = \Pingpp\Transfer::create(
           array(
                'order_no'    => $trade->id,
                'app'         => array('id' => env('PINGPP_OP')),
                'channel'     => 'wx',
                'amount'      => $amount,
                'currency'    => $currency,
                'type'        => 'b2c',
                'recipient'   => $open_id,
                'description' => '图派红包提现,绽放你的灵感'
            )
        );
        return $this->output($trans);
    }

    private function checkAmount($amount, $type = mUserLanding::TYPE_WEIXIN) {
        if (!$amount) {
            return error('AMOUNT_NOT_EXIST');
        }
        //提现逻辑
        if ($amount > 200 * config('global.MULTIPLIER')) {
            return error('AMOUNT_ERROR', '单次提现金额不能大于200，提现失败');
        }

        $user = sUser::getUserByUid($this->_uid);
        if ($amount > $user->balance) {
            return error('AMOUNT_ERROR', '余额不足，提现失败');
        }

        return $user;
    }
}
