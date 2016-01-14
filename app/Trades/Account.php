<?php namespace App\Trades;

use App\Services\User as sUser;

class Account extends TradeBase {
    protected $connection   = 'db_trade';
    public $table           = 'accounts';
    //成功
    const ACCOUNT_SUCCEED_STATUS = 1;
    //余额不足
    const ACCOUNT_FAIL_STATUS = 2;
    public $keys = array(
        'balance',
        'type',
        'amount',
        'memo',
        'status'
    );

    public function setAmountAttribute( $value )
    {
        if(!is_double($value)) {
            return error('WRONG_ARGUMENTS', '收入需要为浮点数');
        }
        $this->attributes['amount'] = $value;
    }
    public function setBalanceAttribute( $value )
    {
        if(!is_double($value)) {
            return error('WRONG_ARGUMENTS', '账户余额需要为浮点数');
        }
        $this->attributes['balance'] = $value;
    }

    /*
     * 用户资产流水 - 冻结
     */
    public function freezeAccount($uid, $amount, $balance, $status, $memo = '成功')
    {
        $tAccount = new tAccount($uid);
        $tAccount->setBalance($balance)
            ->setType(tAccount::ACCOUNT_OPERATE_TYPE_FREEZE)
            ->setMemo($memo)
            ->setStatus($status)
            ->setAmount($amount)
            ->save();
        return $tAccount;
    }
}
