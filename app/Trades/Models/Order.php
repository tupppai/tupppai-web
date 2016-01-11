<?php namespace App\Trades\Models;

class Order extends ModelBase {
    protected $connection   = 'db_trade';
    public $table           = 'orders';

    public function beforeSave() {
        if(!is_double($this->balance)) {
            return error('WRONG_ARGUMENTS', '账户余额需要为浮点数');
        }
    }
    
    public function get_order_by_id($id) {
        return $this->find($id);
    }
}
