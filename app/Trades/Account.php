<?php namespace App\Trades;

use App\Services\User as sUser;

class Account extends TradeBase {
    protected $connection   = 'db_trade';
    public $table           = 'accounts';
    //成功
    const STATUS_ACCOUNT_SUCCEED = 1;
    //失败
    const STATUS_ACCOUNT_FAIL = 2;
    public $keys = array(
        'balance',
        'type',
        'amount',
        'memo',
        'status'
    );

    /**
     * 设置的时候需要校验属性
     */
    public function setAmountAttribute( $value )
    {
        $this->attributes['amount'] = $value/1000;
    }
    public function setBalanceAttribute( $value )
    {
        $this->attributes['balance'] = $value/1000;
    }
    public function getAmountAttribute( )
    {
        $this->attributes['amount'] *= 1000;
    }
    public function getBalanceAttribute( )
    {
        $this->attributes['balance'] *= 1000;
    }

    /**
     * 设置余额的时候判断是否为浮点数
     */
    public function setBalance($value) {
        if(!is_double($value)) {
            return error('WRONG_ARGUMENTS', '收入需要为浮点数');
        }
    }

    /**
     * 设置交易金额的时候判断是否为浮点数
     */
    public function setAmount($value) {
        if(!is_double($value)) {
            return error('WRONG_ARGUMENTS', '收入需要为浮点数');
        }
    }

    /*
     * 用户资产流水 - 冻结
     */
    public static function freezeAccount($uid, $amount, $balance, $status, $memo = '成功')
    {
        $tAccount = new self($uid);
        $tAccount->setBalance($balance)
            ->setType(self::TYPE_FREEZE_ACCOUNT)
            ->setMemo($memo)
            ->setStatus($status)
            ->setAmount($amount)
            ->save();
        return $tAccount;
    }
}
