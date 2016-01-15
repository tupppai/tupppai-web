<?php namespace App\Trades;

use App\Services\User as sUser;

class Account extends TradeBase
{
    protected $connection = 'db_trade';
    public $table = 'accounts';
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
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 1000;
    }

    public function setBalanceAttribute($value)
    {
        $this->attributes['balance'] = $value * 1000;
    }

    public function getAmountAttribute($value)
    {
        return $value / 1000;
    }

    public function getBalanceAttribute($value)
    {
        return $value / 1000;
    }

    /**
     * 设置余额的时候判断是否为浮点数
     */
    public function setBalance($value)
    {
        $this->balance = $value;
        return $this;
    }

    /**
     * 设置交易金额的时候判断是否为浮点数
     */
    public function setAmount($value)
    {
        $this->amount = $value;
        return $this;
    }

    /*
     * 用户资产流水 - 冻结
     */
    public static function wirteAccount($uid, $amount, $balance, $status, $type, $memo = '成功')
    {
        $tAccount = new self($uid);
        $tAccount->setBalance($balance)
            ->setType($type)
            ->setMemo($memo)
            ->setStatus($status)
            ->setAmount($amount)
            ->save();
        return $tAccount;
    }
}
