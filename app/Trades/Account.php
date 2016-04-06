<?php namespace App\Trades;

use App\Trades\Transaction as tTransaction;
use App\Trades\User as tUser;
use PingppLog;

class Account extends TradeBase
{
    protected $connection = 'db_trade';
    public $table = 'accounts';

    public $keys = array(
        'uid',
        'balance',
        'type',
        'amount',
        'memo',
        'extra',
        'status'
    );

    /*
     * 用户资产流水 - 冻结
     */
    public static function writeLog($uid, $amount, $balance, $status, $type, $memo = '成功', $extra = '')
    {
        $tAccount = new self;
        $tAccount->setBalance($balance)
            ->setUid($uid)
            ->setType($type)
            ->setMemo($memo)
            ->setStatus($status)
            ->setAmount($amount)
            ->setExtra($extra)
            ->save();
        return $tAccount;
    }

    public static function updateStatus($account_id, $status) {
        $tAccount = self::find($account_id);
        return $tAccount->setStatus($status)
            ->save();
    }

    /**
     * 用户提现操作
     */
    public static function withdraw($uid, $open_id, $amount, $type = 'wx', $phone = '') {
        $subject = '图派';
        $body    = '提现';
        $currency= 'cny';

        $account = tUser::reduceBalance($uid, $amount, "$subject-$body", $open_id);
        $attach  = array(
            'account_id'=>$account->id,
            'open_id'=>$open_id,
            'uid'=>$uid,
            'amount'=>$amount,
            'phone'=>$phone,
            'type'=>$type
        );

        $trade = tTransaction::writeLog($uid, '', '', tTransaction::PAYMENT_TYPE_WECHAT, $amount, tTransaction::STATUS_PENDING, $subject, $body, $currency, $attach);

        return $trade;
    }

    /**
     * 提现失败，拒绝提现
     */
    public static function refuse($trade_id, $remark = '') {
        
        $trade = tTransaction::find($trade_id);
        if(!$trade) {
            return error('TRADE_NOT_EXIST');
        }

        $trade->setPaymentType(tTransaction::PAYMENT_TYPE_WECHAT)
            ->setOperator(_uid())
            ->setTradeStatus(self::STATUS_FAILED)
            ->setOpRemark($remark);

        $trade->save();

        $uid     = $trade->uid;
        $amount  = $trade->amount;
        $open_id = $trade->attach->open_id;
        $account = tUser::addBalance($uid, $amount, "提现失败，系统入账[$remark]", $open_id);

        return $trade;
    }

    //================== ping++ 支付 =====================
    /**
     * 微信企业转账
     */
    public static function b2c($trade_id, $remark = '') {
        $trade = tTransaction::find($trade_id);
        if(!$trade) {
            return error('TRADE_NOT_EXIST');
        }

        \Pingpp\Pingpp::setApiKey(env('PINGPP_KEY'));
        $trans = \Pingpp\Transfer::create(
           array(
                'order_no'    => $trade->trade_no,
                'app'         => array('id' => env('PINGPP_OP')),
                'channel'     => $trade->attach->type,
                'amount'      => $trade->attach->amount,
                'currency'    => $trade->currency_type,
                'type'        => 'b2c',
                'recipient'   => $trade->attach->open_id,
                'description' => '企业支付提现,绽放你的灵感'
            )
        );
        if($trans->status == 'pending') {
            $trade->setPaymentType(tTransaction::PAYMENT_TYPE_WECHAT_TRANSFER)
                ->setOperator(_uid())
                ->setTradeStatus(self::STATUS_UNCERTAIN) //不确定订单
                ->setOpRemark($remark)
                ->save();
        }
        else {
            $trade->setOpRemark($trans->failure_msg)
                ->setTradeStatus(self::STATUS_FAILED)
                ->save();
        }
        PingppLog::addInfo( $trans );

        return $trans;
    }

    /**
     * 微信红包提现
     */
    public static function red($trade_id, $remark = '') {
        $trade = tTransaction::find($trade_id);
        if(!$trade) {
            return error('TRADE_NOT_EXIST');
        }

        $trade->setPaymentType(tTransaction::PAYMENT_TYPE_WECHAT_RED)
            ->setOperator(_uid())
            ->setTradeStatus(self::STATUS_PAYING)
            ->setOpRemark($remark);
        $trade->save();

        \Pingpp\Pingpp::setApiKey(env('PINGPP_KEY'));
        $red = \Pingpp\RedEnvelope::create(
            array(
                'order_no'    => $trade->trade_no,
                'app'         => array('id' => env('PINGPP_OP')),
                'channel'     => $trade->attach->type, 
                'amount'      => $trade->attach->amount,
                'currency'    => $trade->currency_type,
                'subject'     => $trade->subject,
                'body'        => $trade->body,
                'extra'       => array(
                    'nick_name' => '图派',
                    'send_name' => '皮埃斯网络科技'
                ),//extra 需填入的参数请参阅 API 文档
                'recipient'   => $trade->attach->open_id,
                'description' => '红包提现,绽放你的灵感'
            )
        );
        if($red->status == 'pending') {
            $trade->setPaymentType(tTransaction::PAYMENT_TYPE_WECHAT_TRANSFER)
                ->setOperator(_uid())
                ->setTradeStatus(self::STATUS_UNCERTAIN) //不确定订单
                ->setOpRemark($remark)
                ->save();
        }
        else {
            $trade->setOpRemark($red->failure_msg)
                ->setTradeStatus(self::STATUS_FAILED)
                ->save();
        }
        PingppLog::addInfo( $red );

        return $red;
    }

    /**
     * 多平台支付
     */
    public static function pay($uid, $amount, $type = 'wx', $data = array()) {
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
            //todo: 找到open_id
            $extra['open_id'] = $data['open_id'];
        }
        else if($type == 'alipay') {
            $payment_type = tTransaction::PAYMENT_TYPE_ALIPAY;
        }

        $trade  = tTransaction::writeLog($uid, '', '', $payment_type, $amount, tTransaction::STATUS_PAYING, $subject, $body, $currency, $attach);
       
        $charge = \Pingpp\Charge::create(array(
            'order_no'  => $trade->trade_no,
            'amount'    => $amount,
            'app'       => array('id' => env('PINGPP_OP')),
            'channel'   => $type,
            'currency'  => $currency,
            'client_ip' => $trade->client_ip,
            'subject'   => $subject,
            'body'      => $body ,
            'extra'     => $extra
        ));
        PingppLog::addInfo( $charge );

        return $charge;
    }
}
