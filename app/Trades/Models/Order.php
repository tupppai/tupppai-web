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

    /**
     * 生成订单
     */
    public function create_order() {
        //生成订单号
    }
    
    public function set_price($price) {
    }

    public function set_title($title) {
    }

    public function set_payment_type($payment_type) {
    }

    public function set_discount_id($discount_id) {
    }

    public function set_handling_fee($handling_fee) {
    }

    public function set_order_info($order_info) {
    }

    public function set_operator($uid) {
    }


}
