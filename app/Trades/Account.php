<?php namespace App\Trades;

use App\Services\User as sUser;

use App\Trades\Transaction as tTransaction;
use App\Trades\User as tUser;

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

    //================== ping++ 支付 =====================
    /**
     * 企业转账
     */
    public static function b2c($uid, $open_id, $amount) {
        $subject = '图派';
        $body    = '红包提现';
        $currency= 'cny';


        $account = tUser::reduceBalance($uid, $amount, "$subject-$body", $open_id);
        $attach  = array(
            'account_id'=>$account->id,
            'open_id'=>$open_id,
            'uid'=>$uid,
            'amount'=>$amount
        );

        $trade = tTransaction::writeLog($uid, '', '', tTransaction::PAYMENT_TYPE_WECHAT_TRANSFER, $amount, tTransaction::STATUS_PAYING, $subject, $body, $currency, $attach);

        $trans = \Pingpp\Transfer::create(
           array(
                'order_no'    => $trade->trade_no,
                'app'         => array('id' => env('PINGPP_OP')),
                'channel'     => 'wx',
                'amount'      => $amount,
                'currency'    => $currency,
                'type'        => 'b2c',
                'recipient'   => $open_id,
                'description' => '图派红包提现,绽放你的灵感'
            )
        );

        return $trans;
    }

    /**
     * 红包提现
     */
    public static function red($uid, $open_id, $amount) {
        $subject = '图派';
        $body    = '红包提现';
        $currency= 'cny';

        $account = tUser::reduceBalance($uid, $amount, "$subject-$body", $open_id);
        $attach  = array(
            'account_id'=>$account->id,
            'open_id'=>$open_id,
            'uid'=>$uid,
            'amount'=>$amount
        );

        $trade = tTransaction::writeLog($uid, '', '', tTransaction::PAYMENT_TYPE_WECHAT_RED, $amount, tTransaction::STATUS_PAYING, $subject, $body, $currency, $attach);

        $red = \Pingpp\RedEnvelope::create(
            array(
                'order_no'    => $trade->trade_no,
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

        return $red;
    }

    /**
     * 支付
     */
    public static function pay($uid, $open_id, $amount, $type = 'wx') {
        $subject = '图派';
        $body    = '充值';
        $currency= 'cny';

        $attach  = array(
            'open_id'=>$open_id,
            'uid'=>$uid,
            'amount'=>$amount
        );

        $trade  = tTransaction::writeLog($uid, '', '', tTransaction::PAYMENT_TYPE_WECHAT, $amount, tTransaction::STATUS_PAYING, $subject, $body, $currency, $attach);
       
        $charge = \Pingpp\Charge::create(array(
            'order_no'  => $trade->trade_no,
            'amount'    => $amount,
            'app'       => array('id' => env('PINGPP_OP')),
            'channel'   => $type,
            'currency'  => 'cny',
            'client_ip' => $trade->client_ip,
            'subject'   => $subject,
            'body'      => $body 
        ));

        return $charge;
    }
}
