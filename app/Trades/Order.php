<?php namespace App\Trades\Models;

class Order extends ModelBase {
    protected $connection   = 'db_trade';
    public $table           = 'orders';

    public $keys = array(
        'order_no',
        'order_type',
        'trade_type',
        'sale_type',
        'payment_type',
        'total_amount',
        'discount_id',
        'discount_amount',
        'handling_fee',
        'order_info',
        'operator',
        'op_remark'
    );

    /**
     * 生成订单
     */
    public function create($uid) {
        //生成订单号
        $this->order_no = $this->create_order_no($uid);
        $this->uid      = $uid;

        return $this;
    }

    private function create_order_no($uid, $type = '1') {
        //重新定义订单号规则
        return $type.$uid.date("YmdHis");
    }

    public function beforeSave() {
        if(!is_double($this->balance)) {
            return error('WRONG_ARGUMENTS', '账户余额需要为浮点数');
        }
    }
    
    public function get_order_by_id($id) {
        return $this->find($id);
    }
}
