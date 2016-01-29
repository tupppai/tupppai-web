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
}
