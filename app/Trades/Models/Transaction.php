<?php namespace App\Trades\Models;

class Transaction extends ModelBase {
    protected $connection   = 'db_trade';
    public $table           = 'transactions';

    public function beforeSave() {
        if(!is_double($this->balance)) {
            return error('WRONG_ARGUMENTS', '账户余额需要为浮点数');
        }
    }
    
    public function get_order_by_id($id) {
        return $this->find($id);
    }
    
    public function set_out_trade_no($out_trade_no) {
    }

    public function set_parter_id($parter_id) {
    }

    public function set_payment_type($payment_type) {
    }

    public function set_amount($amount) {
    }
}
