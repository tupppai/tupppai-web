<?php namespace App\Http\Controllers\Api;

use App\Services\User as sUser;
use App\Services\UserLanding as sUserLanding;

use App\Trades\Transaction as tTransaction;
use App\Trades\User as tUser;
use App\Trades\Account as tAccount;

use App\Models\UserLanding as mUserLanding;

use Queue, App\Jobs\Push;

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
    
    public function rewardAction()
    {
        $uid    = $this->_uid;
        $ask_id = $this->post( 'ask_id', 'int');
        $amount = $this->post( 'amount', 'int');
        $type   = $this->post( 'type', 'string', 'wx');

        if(empty($ask_id) || empty($uid)){
            return error('EMPTY_ARGUMENTS');
        }

        $data = '';
        //生成随机打赏金额
        $amount = $amount ? $amount : randomFloat(config('global.reward_amount_scope_start'), config('global.reward_amount_scope_end'));
        try {
            //打赏
            $reward = sReward::createReward($uid, $ask_id ,$amount, mUserLanding::STATUS_READY);
            $data   = tAccount::pay($this->_uid, $open_id, $amount, $type, array(
                'reward_id'=>$reward_id
            ));
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

        $open_id = $this->open_id;
        $data    = '';

        try {
            $data = tAccount::pay($this->_uid, $open_id, $amount, $type);
            Queue::push(new Push(array(
                'uid'=>$this->_uid,
                'from_uid'=> tUser::SYSTEM_USER_ID,
                'type'=>'self_recharge',
                'amount' => money_convert( $amount )
            )));
        } catch (\Exception $e) {
            return error('TRADE_PAY_ERROR', $e->getMessage());
        }

        return $this->output($data);
    }

    /**
     * 提现
     */
    public function transferAction() {
        $type    = $this->post('type', 'string', 'red');
        $amount  = $this->post('amount', 'money');

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

        $open_id = $this->open_id;
        $data    = '';

        try {
            if($type == 'red') {
                $data = tAccount::b2c($this->_uid, $open_id, $amount);
            }
            else {
                $data = tAccount::red($this->_uid, $open_id, $amount);
            }
        }
        catch (\Exception $e) {
            return error('TRADE_PAY_ERROR', $e->getMessage());
        }

        return $this->output($data);
    }

}
