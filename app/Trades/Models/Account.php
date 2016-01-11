<?php namespace App\Trades\Models;

class Account extends ModelBase {
    protected $connection   = 'db_trade';
    public $table           = 'accounts';

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
    
    public function get_account_by_uid($uid) {
        return $this->where('uid', $uid)->first();
    }
}
