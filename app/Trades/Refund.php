<?php namespace App\Trades;

class Refund extends TradeBase {
    protected $connection   = 'db_trade';
    public $table           = 'refunds';

    public function get_order_by_id($id) {
        return $this->find($id);
    }
}
