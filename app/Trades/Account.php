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
