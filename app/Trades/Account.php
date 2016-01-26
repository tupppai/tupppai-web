<?php namespace App\Trades;

use App\Services\User as sUser;

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
        'status'
    );

    /**
     * 设置的时候需要校验属性
     */
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * config('global.MULTIPLIER');
    }

    public function setBalanceAttribute($value)
    {
        $this->attributes['balance'] = $value * config('global.MULTIPLIER');
    }

    public function getAmountAttribute($value)
    {
        return $value / config('global.MULTIPLIER');
    }

    public function getBalanceAttribute($value)
    {
        return $value / config('global.MULTIPLIER');
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
    public static function writeLog($uid, $amount, $balance, $status, $type, $memo = '成功')
    {
        $tAccount = new self;
        $tAccount->setBalance($balance)
            ->setUid($uid)
            ->setType($type)
            ->setMemo($memo)
            ->setStatus($status)
            ->setAmount($amount)
            ->save();
        return $tAccount;
    }
}
