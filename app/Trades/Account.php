<?php namespace App\Trades;

use App\Services\User as sUser;

class Account extends TradeBase {
    protected $connection   = 'db_trade';
    public $table           = 'accounts';

    public $keys = array(
        'balance',
        'income_amount',
        'outcome_amount',
        'freeze_amount',
        'memo',
        'status'
    );

    public function beforeSave() {
        if(!is_double($this->balance)) {
            return error('WRONG_ARGUMENTS', '账户余额需要为浮点数');
        }
        if(!is_double($this->income_amount)) {
            return error('WRONG_ARGUMENTS', '收入需要为浮点数');
        }
        if(!is_double($this->outcome_amount)) {
            return error('WRONG_ARGUMENTS', '支出需要为浮点数');
        }
        if(!is_double($this->freeze_amount)) {
            return error('WRONG_ARGUMENTS', '冻结金额需要为浮点数');
        }
    }
    
    public function __construct($uid) {
        parent::__construct();

        $this->uid = $uid;
        return $this;
    }
}
