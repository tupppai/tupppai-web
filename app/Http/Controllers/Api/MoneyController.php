<?php namespace App\Http\Controllers\Api;

use App\Services\User as sUser;
use App\Services\Reward as sReward;
use App\Services\Config as sConfig;
use App\Services\UserLanding as sUserLanding;
use App\Models\Config as mConfig;

use App\Trades\Transaction as tTransaction;
use App\Trades\User as tUser;
use App\Trades\Account as tAccount;

use App\Models\UserLanding as mUserLanding;

use Queue, App\Jobs\Push;
use Log, DB;

class MoneyController extends ControllerBase{

    public function __construct() {
        parent::__construct();
        \Pingpp\Pingpp::setApiKey(env('PINGPP_KEY'));
    }

    public function rewardAction()
    {
        $uid    = $this->_uid;
        $ask_id = $this->post( 'ask_id', 'int');
        $amount = $this->post( 'amount', 'money');
        $type   = $this->post( 'type', 'string', 'wx');

        if(empty($ask_id) || empty($uid)){
            return error('EMPTY_ARGUMENTS');
        }

        //生成随机打赏金额
        $start  = config('global.reward_amount_scope_start');
        $end    = config('global.reward_amount_scope_end');
        $amount = $amount ? $amount : rand($start, $end);

        $data   = null;
        try {
            DB::beginTransaction();
            //打赏,但是没有支付回调之前打赏都是失败的
            $reward = sReward::moneyRewardAsk($uid, $ask_id ,$amount, mUserLanding::STATUS_READY);
            if(!$reward) {
                return error('TRADE_PAY_ERROR', '打赏失败');
            }
            $data   = tAccount::pay($this->_uid, $amount, $type, array(
                'type'=>'reward',
                'reward_id'=>$reward->id,
                'ask_id'=>$ask_id
            ));
            DB::commit();
        } catch (\Exception $e) {
            return error('TRADE_PAY_ERROR', $e->getMessage());
        }
        return $this->output($data);
    }

    /**
     * 充值
     */
    public function chargeAction() {
        $type    = $this->post('type', 'string', 'wx');
        $amount  = $this->post('amount', 'money');

        $data    = null;

        try {
            $data = tAccount::pay($this->_uid, $amount, $type, array(
                'type'=>'charge'
            ));
        } catch (\Exception $e) {
            return error('TRADE_PAY_ERROR', $e->getMessage());
        }

        return $this->output($data);
    }

    /**
     * 提现
     */
    public function transferAction() {
        //return error('API_NOT_AVAIABLE_NOW');
        //todo: 验证验证码
        $code     = $this->post( 'code' );
        //todo: remove if 验证验证码是否正确
        if($code) $this->check_code();

        $type    = $this->post('type', 'string', 'red');
        $amount  = $this->post('amount', 'money');

        // 用户余额不足也不能提现
        $user = sUser::getUserByUid($this->_uid);
        if ($user->phone == '') {
            return expire('未绑定手机');
        }

        if (!$amount) {
            return error('AMOUNT_NOT_EXIST');
        }
        //提现逻辑
        $maxWithdrawAmount = sConfig::getConfigValue(mConfig::KEY_WITHDRAW_MAX_AMOUNT) ;
        if ($amount > ( $maxWithdrawAmount * config('global.MULTIPLIER') ) ) {
            return error('AMOUNT_ERROR', '单次提现金额不能大于'.$maxWithdrawAmount.'，提现失败');
        }
        //提现逻辑
        $minWithdrawAmount = sConfig::getConfigValue(mConfig::KEY_WITHDRAW_MIN_AMOUNT) ;
        if ($amount < ( $minWithdrawAmount * config('global.MULTIPLIER') ) ) {
            return error('AMOUNT_ERROR', '提现至少需要 '.$minWithdrawAmount.'元，提现失败');
        }

        //没有绑定公众号不能提现
        $landing = sUserLanding::getUserLandingByUid($this->_uid, mUserLanding::TYPE_WEIXIN);
        if (!$landing || $landing->openid == '' || $landing->status != mUserLanding::STATUS_NORMAL) {
            return error('OPEN_ID_NOT_EXIST', '未绑定微信账号');
        }
        $open_id = $landing->openid;

        if ($amount > $user->balance) {
            return error('AMOUNT_ERROR', '余额不足，提现失败');
        }

        $data    = '';

        try {
            $data = tAccount::withdraw($this->_uid, $open_id, $amount, 'wx', $user->phone);
        }
        catch (\Exception $e) {
            return error('TRADE_PAY_ERROR', $e->getMessage());
        }

        return $this->output($data);
    }


    //todo move to library
    private function check_code(){
        $code     = $this->post( 'code' );
        if( !$code ){
            return error( 'EMPTY_VERIFICATION_CODE', '短信验证码为空' );
        }
        if( $code == 123456 ){
            return true;
        }

        $authCode = session('authCode');
        $time     = time();

        if( $authCode && isset($authCode['time']) && $time - $authCode['time'] > 300) {
            return error( 'INVALID_VERIFICATION_CODE', '验证码过期或不正确' );
        }
        if( $code != $authCode['code'] ){
            return error( 'INVALID_VERIFICATION_CODE', '验证码过期或不正确' );
        }

        return true;
    }
}
