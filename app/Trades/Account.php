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

    public function setIncomeAmountAttribute( $value )
    {
        if(!is_double($value)) {
            return error('WRONG_ARGUMENTS', '收入需要为浮点数');
        }
    }
    public function setBalanceAttribute( $value )
    {
        if(!is_double($value)) {
            return error('WRONG_ARGUMENTS', '账户余额需要为浮点数');
        }
        $this->attributes['balance'] = $value;
    }
    public function setOutcomeAmountAttribute($value)
    {
        if(!is_double($value)) {
            return error('WRONG_ARGUMENTS', '支出需要为浮点数');
        }
    }
    public function setFreezeAmountAttribute($value)
    {
        if(!is_double($value)) {
            return error('WRONG_ARGUMENTS', '冻结金额需要为浮点数');
        }
    }

}
