<?php namespace App\Trades;

use App\Trades\Models\Order as mOrder;

class Order extends TradeBase {

    /**
     * 产生订单号
     */
    public function genOrderNo() {

    }

    /**
     * 通过id获取订单信息
     */
    public static function getOrderByid($uid) {
        return (new mOrder)->get_order_by_id($uid);
    }

}
