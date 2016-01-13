<?php namespace App\Trades\Models;

class Transaction extends ModelBase {
    protected $connection   = 'db_trade';
    public $table           = 'transactions';
    
    public $keys = array(
        'trade_no',
        'out_trade_no',
        'order_id', 
        'partner_id', 
        'payment_type',
        'amount',
        'trade_status',
        'trade_start_time',
        'trade_finish_time',
        'callback_status',
        'callback_finish_time',
        'refund_status',
        'refund_start_time',
        'refund_finish_time',
        'time_start',
        'time_expire',
        'return_url',
        'fail_url',
        'notify_url',
        'subject',
        'body',
        'client_ip',
        'attach',
        'operator',
        'op_remark',
        'status'
    );

    

    /**
     * 生成订单
     */
    public function create($uid, $order_id) {
        //生成订单号
        $this->trade_no = $this->create_order_no($uid, $order_id);
        $this->uid      = $uid;

        return $this;
    }

    private function create_trade_no($uid, $order_id) {
        //更新交易单号规则
        return md5($order_id.rand());
    }
    
    public function beforeSave() {
        if(!is_double($this->balance)) {
            return error('WRONG_ARGUMENTS', '账户余额需要为浮点数');
        }
    }
}
